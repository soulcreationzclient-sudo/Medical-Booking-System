<?php

namespace App\Policies;

use App\Models\Specialization;
use App\Models\User;

class Deleteupdatespecializationpolicy
{
    /**
     * Create a new policy instance.
     */
    public function update(User $user, Specialization $specialization): bool
    {
        return $user->role === 'hospital_admin'
            && $user->hospital_id === $specialization->hospital_id;
    }

    public function delete(User $user, Specialization $specialization): bool
    {
        return $user->role === 'hospital_admin'
            && $user->hospital_id === $specialization->hospital_id;
    }
}
