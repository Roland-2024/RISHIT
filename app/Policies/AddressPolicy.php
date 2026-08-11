<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    public function update(User $user, Address $address): bool
    {
        return $user->is($address->user);
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->update($user, $address);
    }
}
