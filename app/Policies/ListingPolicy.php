<?php

namespace App\Policies;

use App\Domain\Catalog\ListingStatus;
use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function update(User $user, Listing $listing): bool
    {
        return $user->is($listing->user)
            && $listing->status !== ListingStatus::Reserved
            && ! $listing->activeOrder()->exists();
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $this->update($user, $listing);
    }
}
