<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GateLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class GateLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GateLog');
    }

    public function view(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('View:GateLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GateLog');
    }

    public function update(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('Update:GateLog');
    }

    public function delete(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('Delete:GateLog');
    }

    public function restore(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('Restore:GateLog');
    }

    public function forceDelete(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('ForceDelete:GateLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GateLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GateLog');
    }

    public function replicate(AuthUser $authUser, GateLog $gateLog): bool
    {
        return $authUser->can('Replicate:GateLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GateLog');
    }

}