<?php

namespace App\Console\Commands;

use App\Models\ParkingReservation;
use App\Models\ParkingSlot;
use Illuminate\Console\Command;

class TrimParkingSlots extends Command
{
    protected $signature = 'parking:trim
        {--max=4 : Keep slot_number <= max per area}
        {--force : Delete slots even if they have active reservations (will cascade delete the reservations)}
        {--dry : Show what would be deleted without deleting}';

    protected $description = 'Reduce the number of parking slots per area by deleting slot_number > --max';

    public function handle(): int
    {
        $max = (int) $this->option('max');
        $force = (bool) $this->option('force');
        $dry = (bool) $this->option('dry');

        if ($max < 1) {
            $this->error('--max must be >= 1');
            return self::INVALID;
        }

        $candidates = ParkingSlot::where('slot_number', '>', $max)->get();

        if ($candidates->isEmpty()) {
            $this->info("Nothing to do. No slots with slot_number > {$max}.");
            return self::SUCCESS;
        }

        $this->line("Found {$candidates->count()} slot(s) above slot_number {$max}:");

        $toDelete = [];
        $blocked = [];

        foreach ($candidates as $slot) {
            $activeCount = ParkingReservation::where('parking_slot_id', $slot->id)
                ->where('status', 'active')
                ->count();

            $row = [
                'id' => $slot->id,
                'area' => $slot->area,
                'slot_number' => $slot->slot_number,
                'status' => $slot->status,
                'active_reservations' => $activeCount,
            ];

            if ($activeCount > 0 && ! $force) {
                $blocked[] = $row;
            } else {
                $toDelete[] = $row;
            }
        }

        $this->table(
            ['id', 'area', 'slot_number', 'status', 'active_reservations'],
            array_merge($toDelete, $blocked)
        );

        if (! empty($blocked)) {
            $this->warn(count($blocked) . ' slot(s) skipped because they have active reservations. Re-run with --force to delete them anyway.');
        }

        if (empty($toDelete)) {
            $this->info('Nothing to delete.');
            return self::SUCCESS;
        }

        if ($dry) {
            $this->comment('[--dry] Would delete ' . count($toDelete) . ' slot(s). No changes made.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Delete ' . count($toDelete) . ' slot(s)? This also cascades reservations.')) {
            $this->comment('Aborted.');
            return self::SUCCESS;
        }

        $ids = array_column($toDelete, 'id');
        ParkingSlot::whereIn('id', $ids)->delete();

        $this->info('Deleted ' . count($ids) . ' parking slot(s).');

        return self::SUCCESS;
    }
}
