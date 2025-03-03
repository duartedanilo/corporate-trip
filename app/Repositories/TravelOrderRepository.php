<?php

namespace App\Repositories;

use App\Models\TravelOrder;

class TravelOrderRepository
{
    public function getTravelOrderFromAuthenticatedUser(int $requester)
    {
        return TravelOrder::where('requester', $requester)->get();
    }
}
