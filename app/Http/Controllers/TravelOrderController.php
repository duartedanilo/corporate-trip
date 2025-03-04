<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelOrderRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Mail\TravelOrderStatusUpdated;
use App\Repositories\TravelOrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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

        if (in_array($validated['status'], ['cancelled', 2]) && $travelOrder->status === 'approved' && Carbon::now() > $travelOrder->departure_date) {
            return response()->json(['message' => 'This travel can\'t be cancelled'], 403);
        }

        $travelOrder = $this->repository->updateStatus($id, $validated['status']);

        Mail::to($travelOrder->user)->send(new TravelOrderStatusUpdated($travelOrder, $travelOrder->status));

        return response()->json(['message' => 'Status updated successfully!']);
    }
}
