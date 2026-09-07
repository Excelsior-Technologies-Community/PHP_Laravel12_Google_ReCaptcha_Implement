<?php

namespace App\Policies;

use App\Models\SecurityLog;
use App\Models\User;

class SecurityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SecurityLog $securityLog): bool
    {
        return true;
    }
}
