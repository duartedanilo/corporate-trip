<?php

namespace App\Repositories;

use App\Models\TravelOrder;

class TravelOrderRepository
{
    public function getTravelOrderFromAuthenticatedUser(int $requester)
    {
        return TravelOrder::where('requester', $requester)->get();
    }

    public function create(array $data)
    {
        return TravelOrder::create($data);
    }

    public function findById(int $id)
    {
        return TravelOrder::findOrFail($id);
    }
}
