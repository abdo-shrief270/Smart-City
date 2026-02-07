<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParkingSlot;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParkingSlotPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParkingSlot');
    }

    public function view(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('View:ParkingSlot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParkingSlot');
    }

    public function update(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('Update:ParkingSlot');
    }

    public function delete(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('Delete:ParkingSlot');
    }

    public function restore(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('Restore:ParkingSlot');
    }

    public function forceDelete(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('ForceDelete:ParkingSlot');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParkingSlot');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParkingSlot');
    }

    public function replicate(AuthUser $authUser, ParkingSlot $parkingSlot): bool
    {
        return $authUser->can('Replicate:ParkingSlot');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParkingSlot');
    }

}