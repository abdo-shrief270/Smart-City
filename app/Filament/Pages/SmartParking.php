<?php

namespace App\Filament\Pages;

use App\Models\ParkingReservation;
use App\Models\ParkingSlot;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Services\FirebaseService;

class SmartParking extends Page implements HasActions
{
    use InteractsWithActions;
    use HasPageShield;

    protected string $view = 'filament.pages.smart-parking';

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

    public array $parkingSlots = [];
    public int $totalSlots = 0;
    public int $availableSlots = 0;
    public int $occupiedSlots = 0;
    public float $totalRevenue = 0.0;

    // Live aggregate counts reported by the IoT parking sensor
    // (schema: SmartParking/FreeSlots, SmartParking/OccupiedSlots).
    public int $liveFreeSlots = 0;
    public int $liveOccupiedSlots = 0;

    public function mount(): void
    {
        $this->refreshSlots();
    }

    public function refreshSlots(): void
    {
        // Read live sensor counts from Firebase.
        $sensor = app(FirebaseService::class)->get('SmartParking', fresh: true);
        if (is_array($sensor)) {
            $this->liveFreeSlots = (int) ($sensor['FreeSlots'] ?? 0);
            $this->liveOccupiedSlots = (int) ($sensor['OccupiedSlots'] ?? 0);
        }

        $allSlots = ParkingSlot::with('activeReservation.user')->get();

        $this->totalSlots = $allSlots->count();
        $this->availableSlots = $allSlots->where('status', 'available')->count();
        $this->occupiedSlots = $this->totalSlots - $this->availableSlots;

        // Calculate revenue from completed reservations today
        $this->totalRevenue = ParkingReservation::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_cost');

        $this->parkingSlots = $allSlots
            ->sortBy(['area', 'slot_number'])
            ->groupBy('area')
            ->toArray();
    }

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
                $slot = ParkingSlot::find($slotId);
                $user = Auth::user();

                if ($user->balance < $slot->cost_per_hour) {
                    Notification::make()
                        ->title('Insufficient Balance')
                        ->body('You need at least $' . number_format($slot->cost_per_hour, 2) . ' to start a reservation.')
                        ->danger()
                        ->send();
                    return;
                }

                if (!$slot || $slot->status !== 'available') {
                    Notification::make()->title('Slot not available')->danger()->send();
                    return;
                }

                $slot->update(['status' => 'reserved']);

                ParkingReservation::create([
                    'user_id' => $user->id,
                    'parking_slot_id' => $slot->id,
                    'start_time' => now(),
                    'status' => 'active',
                ]);

                Notification::make()->title('Slot Reserved Successfully')->success()->send();
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
                $slot = ParkingSlot::with('activeReservation.user')->find($slotId);

                if (!$slot || $slot->status === 'available') {
                    return;
                }

                $reservation = $slot->activeReservation;
                if (!$reservation)
                    return;

                // Calculate Cost
                $endTime = now();
                $startTime = $reservation->start_time;
                $minutes = $startTime->diffInMinutes($endTime);
                $hours = ceil(max($minutes, 1) / 60);
                $cost = $hours * $slot->cost_per_hour;

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
                    'end_time' => $endTime,
                    'total_cost' => $cost,
                    'status' => 'completed',
                ]);

                // Deduct from User Balance
                $user->balance -= $cost;
                $user->save();

                $slot->update(['status' => 'available']);

                Notification::make()
                    ->title('Parking Ended')
                    ->body('Total Cost: $' . number_format($cost, 2) . ' has been deducted from balance.')
                    ->success()
                    ->send();

                $this->refreshSlots();
            });
    }
}
