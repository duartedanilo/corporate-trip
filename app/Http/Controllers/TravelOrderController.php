<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelOrderRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Repositories\TravelOrderRepository;
use Illuminate\Http\Request;

class TravelOrderController extends Controller
{

    public function __construct(private TravelOrderRepository $repository) {}

    public function index()
    {
        $filter = request()->query();

        $requester = auth()->user()->id;
        return $this->repository->getTravelOrderFromAuthenticatedUser($requester, $filter);
    }

    public function store(TravelOrderRequest $request)
    {
        $validated = $request->validated();

        $payload = [
            ...$validated,
            'requester' => auth()->user()->id,
        ];
        return $this->repository->create($payload);
    }

    public function show(string $id)
    {
        return $this->repository->findById($id);
    }

    public function updateStatus(UpdateStatusRequest $request, int $id)
    {
        $validated = $request->validated();

        $travelOrder = $this->repository->findById($id);
        $userId = auth()->user()->id;

        if ($travelOrder->requester == $userId) {
            return response()->json(['message' => 'The requester can\'t change travel order status'], 403);
        }

        $this->repository->updateStatus($id, $validated['status']);

        return response()->json(['message' => 'Status updated successfully!']);
    }
}
