<?php

namespace App\Http\Controllers\TravelOrder;

use App\DTOs\TravelOrderDTO;
use App\Enum\StatusTravelOrderEnum;
use App\Exceptions\TravelOrder\CannotUpdateSelfTravelOrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TravelOrder\AcceptStatusTravelOrderRequest;
use App\Http\Requests\TravelOrder\CancelStatusTravelOrderRequest;
use App\Http\Requests\TravelOrder\StoreTravelOrderRequest;
use App\Http\Resources\TravelOrder\TravelOrderResource;
use App\Interfaces\Services\TravelOrderServiceInterface;
use App\Models\TravelOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TravelOrderController extends Controller
{
    public function __construct(private readonly TravelOrderServiceInterface $travelOrderService)
    {
    }

    public function index()
    {
        //
    }

    public function store(StoreTravelOrderRequest $request): TravelOrderResource | JsonResponse
    {
        try {
            $dto = TravelOrderDTO::fromRequest($request);
            $data = $this->travelOrderService->create($dto);
            return TravelOrderResource::make($data);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(TravelOrder $travelOrder)
    {
        //
    }

    public function accept(int $id, AcceptStatusTravelOrderRequest $request): JsonResponse
    {
        try {
            $this->travelOrderService->updateStatus($id, StatusTravelOrderEnum::getNameById($request->input('status_id')));
            return response()->json(['message' => 'Travel Order accepted successfully!'], Response::HTTP_OK);
        } catch (CannotUpdateSelfTravelOrderException) {
            return response()->json(['message' => 'You cannot update status your self travel order!'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel(int $id, CancelStatusTravelOrderRequest $request): JsonResponse
    {
        try {
            $this->travelOrderService->updateStatus($id, StatusTravelOrderEnum::getNameById($request->input('status_id')));
            return response()->json(['message' => 'Travel Order canceled successfully!'], Response::HTTP_OK);
        } catch (CannotUpdateSelfTravelOrderException) {
            return response()->json(['message' => 'You cannot update status your self travel order!'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
