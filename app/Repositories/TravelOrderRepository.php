<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\TravelOrder;

class TravelOrderRepository
{
    public function getTravelOrderFromAuthenticatedUser(int $requester, array $filter)
    {
        $travelOrder =  TravelOrder::where('requester', $requester);

        if (isset($filter['status'])) {
            $travelOrder->where('status', Status::fromName($filter['status']));
        }

        if (isset($filter['destination'])) {
            $travelOrder->where('destination', $filter['destination']);
        }

        return $travelOrder->get();
    }

    public function create(array $data)
    {
        return TravelOrder::create($data);
    }

    public function findById(int $id)
    {
        return TravelOrder::findOrFail($id);
    }

    public function updateStatus(int $id, string $status)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $travelOrder->status = $status;
        $travelOrder->save();
    }
}
