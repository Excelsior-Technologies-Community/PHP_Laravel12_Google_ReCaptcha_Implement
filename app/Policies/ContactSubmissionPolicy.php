<?php

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return true;
    }

    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return true;
    }
}
