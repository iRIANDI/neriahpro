<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VisionBlueprint;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisionBlueprintPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisionBlueprint');
    }

    public function view(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('View:VisionBlueprint');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VisionBlueprint');
    }

    public function update(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('Update:VisionBlueprint');
    }

    public function delete(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('Delete:VisionBlueprint');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VisionBlueprint');
    }

    public function restore(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('Restore:VisionBlueprint');
    }

    public function forceDelete(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('ForceDelete:VisionBlueprint');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VisionBlueprint');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VisionBlueprint');
    }

    public function replicate(AuthUser $authUser, VisionBlueprint $visionBlueprint): bool
    {
        return $authUser->can('Replicate:VisionBlueprint');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VisionBlueprint');
    }

}