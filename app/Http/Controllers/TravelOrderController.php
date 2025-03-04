<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelOrderRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Services\TravelOrderService;
use Illuminate\Validation\ValidationException;

class TravelOrderController extends Controller
{

    public function __construct(private TravelOrderService $service) {}

    public function index()
    {
        $filter = request()->query();
        $requester = auth()->user()->id;

        return $this->service->index($requester, $filter);
    }

    public function store(TravelOrderRequest $request)
    {
        $validated = $request->validated();

        return $this->service->create(auth()->user()->id, $validated);
    }

    public function show(string $id)
    {
        return $this->service->show($id);
    }

    public function updateStatus(UpdateStatusRequest $request, int $id)
    {
        $validated = $request->validated();

        try {
            $this->service->updateStatus($id, $validated['status']);
            return response()->json(['message' => 'Status updated successfully!']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
