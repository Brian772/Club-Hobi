<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClubPolicy
{
    public function isOwnerOrModerator(User $user, Club $club): bool
    {
        return ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'moderator'])
            ->exists();
    }

    public function isOwner(User $user, Club $club): bool
    {
        return ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }
    public function isModerator(User $user, Club $club): bool
    {
        return ClubMember::where('club_id', $club->id)
            ->where('user_id', $user->id)
            ->where('role', 'moderator')
            ->exists();
    }
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
        return $this->isOwnerOrModerator($user, $club);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Club $club): bool
    {
        return $this->isOwner($user, $club);
    }

    public function ManageMembers(User $user, Club $club): bool
    {
        return $this->isOwnerOrModerator($user, $club);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Club $club): bool
    {
        return $this->isOwner($user, $club);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Club $club): bool
    {
        return $this->isOwner($user, $club);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Club $club): bool
    {
        return $this->isOwner($user, $club);
    }
}
