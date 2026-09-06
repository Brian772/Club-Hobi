<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClubPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Club $club): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function settings(User $user, Club $club): Response
    {
        $member = ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return Response::deny('You are not a member of this club.');
        }
        if ($member->role === 'owner' && $user->id === $club->created_by) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to access the settings of this club.');
    }

    public function edit(User $user, Club $club): Response
    {
        $member = ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return Response::deny('You are not a member of this club.');
        }
        if ($member->role === 'owner' && $user->id === $club->created_by) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to edit this club.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Club $club): Response
    {
        $member = ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return Response::deny('You are not a member of this club.');
        }
        if ($member->role === 'owner' && $user->id === $club->created_by) {
            return Response::allow();
        }
        
        return Response::deny('You do not have permission to update this club.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Club $club): Response
    {
        return $user->id === $club->user_id
            ? Response::allow()
            : Response::deny('You do not own this club.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Club $club): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Club $club): bool
    {
        return false;
    }
}
