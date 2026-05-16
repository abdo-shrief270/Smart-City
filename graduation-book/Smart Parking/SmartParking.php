<?php

namespace App\Filament\Pages;

use App\Models\ParkingReservation;
use App\Models\ParkingSlot;
use App\Services\FirebaseService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SmartParking extends Page implements HasActions
{
    use HasPageShield;
    use InteractsWithActions;

    protected string $view = 'filament.pages.smart-parking';

    // -----------------------------------------------------------------
    // State
    // -----------------------------------------------------------------
    public array $parkingSlots = [];
    public int $totalSlots = 0;
    public int $availableSlots = 0;
    public int $occupiedSlots = 0;
    public float $totalRevenue = 0.0;

    // -----------------------------------------------------------------
    // Navigation
    // -----------------------------------------------------------------
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-truck';
    }

    public static function getNavigationLabel(): string
    {
        return 'Smart Parking';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    // -----------------------------------------------------------------
    // Lifecycle
    // -----------------------------------------------------------------
    public function mount(): void
    {
        $this->refreshSlots();
    }

    public function refreshSlots(): void
    {
        // Sync from Firebase first (read IoT sensor data)
        $firebase    = app(FirebaseService::class);
        $parkingData = $firebase->get('smart-parking');

        if ($parkingData && is_array($parkingData)) {
            foreach ($parkingData as $slotKey => $slotData) {
                if (! is_array($slotData)) {
                    continue;
                }

                // Parse slot key (e.g., "A-1")
                $parts = explode('-', $slotKey);

                if (count($parts) >= 2) {
                    $area       = strtoupper($parts[0]);
                    $slotNumber = (int) $parts[1];

                    $slot = ParkingSlot::where('area', $area)
                        ->where('slot_number', $slotNumber)
                        ->first();

                    if ($slot && ! $slot->activeReservation) {
                        // Update status from IoT sensor
                        $sensorOccupied = (bool) ($slotData['occupied'] ?? false);
                        $newStatus      = $sensorOccupied ? 'occupied' : 'available';

                        if ($slot->status !== $newStatus) {
                            $slot->update(['status' => $newStatus]);
                        }
                    }
                }
            }
        }

        $allSlots = ParkingSlot::with('activeReservation.user')->get();

        $this->totalSlots     = $allSlots->count();
        $this->availableSlots = $allSlots->where('status', 'available')->count();
        $this->occupiedSlots  = $this->totalSlots - $this->availableSlots;

        // Calculate revenue from completed reservations today
        $this->totalRevenue = ParkingReservation::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_cost');

        $this->parkingSlots = $allSlots
            ->sortBy(['area', 'slot_number'])
            ->groupBy('area')
            ->toArray();
    }

    // -----------------------------------------------------------------
    // Actions
    // -----------------------------------------------------------------
    public function reserveSlotAction(): Action
    {
        return Action::make('reserveSlot')
            ->label('Reserve Slot')
            ->requiresConfirmation()
            ->modalHeading('Reserve Parking Slot')
            ->modalDescription('Are you sure you want to reserve this slot starting now?')
            ->action(function (array $arguments) {
                // Balance Check: Must have at least 1 hour cost
                $slotId = $arguments['slot_id'];
                $slot   = ParkingSlot::find($slotId);
                $user   = Auth::user();

                if ($user->balance < $slot->cost_per_hour) {
                    Notification::make()
                        ->title('Insufficient Balance')
                        ->body('You need at least $' . number_format($slot->cost_per_hour, 2) . ' to start a reservation.')
                        ->danger()
                        ->send();

                    return;
                }

                if (! $slot || $slot->status !== 'available') {
                    Notification::make()
                        ->title('Slot not available')
                        ->danger()
                        ->send();

                    return;
                }

                $slot->update(['status' => 'reserved']);

                ParkingReservation::create([
                    'user_id'         => $user->id,
                    'parking_slot_id' => $slot->id,
                    'start_time'      => now(),
                    'status'          => 'active',
                ]);

                // Sync to Firebase
                app(FirebaseService::class)
                    ->updateSlotInFirebase($slot->fresh('activeReservation'));

                Notification::make()
                    ->title('Slot Reserved Successfully')
                    ->success()
                    ->send();

                $this->refreshSlots();
            });
    }

    public function releaseSlotAction(): Action
    {
        return Action::make('releaseSlot')
            ->label('End Parking')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('End Parking Session')
            ->action(function (array $arguments) {
                $slotId = $arguments['slot_id'];
                $slot   = ParkingSlot::with('activeReservation.user')->find($slotId);

                if (! $slot || $slot->status === 'available') {
                    return;
                }

                $reservation = $slot->activeReservation;

                if (! $reservation) {
                    return;
                }

                // Calculate Cost
                $endTime   = now();
                $startTime = $reservation->start_time;
                $minutes   = $startTime->diffInMinutes($endTime);
                $hours     = ceil(max($minutes, 1) / 60);
                $cost      = $hours * $slot->cost_per_hour;

                // Balance Check
                $user = $reservation->user;

                if ($user->balance < $cost) {
                    Notification::make()
                        ->title('Insufficient Balance to Checkout')
                        ->body('Total Cost: $' . number_format($cost, 2) . '. Your Balance: $' . number_format($user->balance, 2) . '. Please top up.')
                        ->danger()
                        ->send();

                    return;
                }

                $reservation->update([
                    'end_time'   => $endTime,
                    'total_cost' => $cost,
                    'status'     => 'completed',
                ]);

                // Deduct from User Balance
                $user->balance -= $cost;
                $user->save();

                $slot->update(['status' => 'available']);

                // Sync to Firebase
                app(FirebaseService::class)
                    ->updateSlotInFirebase($slot->fresh());

                Notification::make()
                    ->title('Parking Ended')
                    ->body('Total Cost: $' . number_format($cost, 2) . ' has been deducted from balance.')
                    ->success()
                    ->send();

                $this->refreshSlots();
            });
    }
}
