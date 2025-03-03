<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelOrderRequest;
use App\Repositories\TravelOrderRepository;
use Illuminate\Http\Request;

class TravelOrderController extends Controller
{

    public function __construct(private TravelOrderRepository $repository) {}

    public function index()
    {
        $requester = auth()->user()->id;
        return $this->repository->getTravelOrderFromAuthenticatedUser($requester);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
