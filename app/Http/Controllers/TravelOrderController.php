<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelOrderRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Services\TravelOrderService;
use Illuminate\Validation\ValidationException;

class TravelOrderController extends Controller
{

    public function __construct(private TravelOrderService $service) {}

    /**
     * @OA\GET(
     *     path="/api/travel-order",
     *     summary="List all travel orders created by this user",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status",
     *         @OA\Schema(
     *              type="string",
     *              enum={"approved", "cancelled", "requested"},
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         description="Destination",
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Start date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-03-01")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="End date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-03-31")
     *     ),
     *     @OA\Response(response="200", description="Get travel orders"),
     *     @OA\Response(response="401", description="Token not valid")
     * )
     */
    public function index()
    {
        $filter = request()->query();
        $requester = auth()->user()->id;

        return $this->service->index($requester, $filter);
    }

    /**
     * @OA\Post(
     *     path="/api/travel-order",
     *     summary="Add new travel order",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         description="Destination",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="departure_date",
     *         in="query",
     *         description="Departure Date",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="return_date",
     *         in="query",
     *         description="Return Date",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="New travel order added"),
     *     @OA\Response(response="401", description="Invalid credentials")
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function store(TravelOrderRequest $request)
    {
        $validated = $request->validated();

        return $this->service->create(auth()->user()->id, $validated);
    }

    /**
     * @OA\Get(
     *     path="/api/travel-order/{id}",
     *     summary="Get travel order information by ID",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the travel order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Get travel order information"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function show(string $id)
    {
        return $this->service->show($id);
    }

    /**
     * @OA\Patch(
     *     path="/api/travel-order/{id}/status",
     *     summary="Update the status of a travel order to 'approved' or 'cancelled' (only admins can do it)",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the travel order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *              enum={"approved", "cancelled"},
     *         )
     *     ),
     *     @OA\Response(response="200", description="Status updated"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="403", description="User is not an admin")
     * )
     */
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
