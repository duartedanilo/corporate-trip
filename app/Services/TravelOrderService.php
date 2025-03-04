<?php

namespace App\Services;

use App\Mail\TravelOrderStatusUpdated;
use App\Repositories\TravelOrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TravelOrderService
{

    public function __construct(private TravelOrderRepository $repository) {}

    public function index()
    {
        $filter = request()->query();

        $requester = auth()->user()->id;
        return $this->repository->getTravelOrderFromAuthenticatedUser($requester, $filter);
    }

    public function create(int $requester, array $data)
    {
        $payload = [...$data, 'requester' => $requester];

        return $this->repository->create($payload);
    }

    public function show(string $id)
    {
        $travelOrder = $this->repository->findById($id);

        if ($travelOrder->requester != Auth::id()) {
            throw ValidationException::withMessages([
                'status' => 'You are not the requester of this travel order.'
            ]);
        }

        return $travelOrder;
    }

    public function updateStatus(int $id, string $status)
    {
        $travelOrder = $this->repository->findById($id);
        $userId = auth()->user()->id;

        if ($travelOrder->requester == $userId) {
            throw ValidationException::withMessages([
                'status' => 'The requester can\'t change travel order status.'
            ]);
        }

        if (in_array($status, ['cancelled', 2]) && $travelOrder->status === 'approved' && Carbon::now() > $travelOrder->departure_date) {
            throw ValidationException::withMessages([
                'status' => 'This travel can\'t be cancelled.'
            ]);
        }

        $this->repository->updateStatus($travelOrder, $status);

        Mail::to($travelOrder->user)->send(new TravelOrderStatusUpdated($travelOrder));

        return response()->json(['message' => 'Status updated successfully!']);
    }
}
