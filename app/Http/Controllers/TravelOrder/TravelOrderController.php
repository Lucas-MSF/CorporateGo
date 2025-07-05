<?php

namespace App\Http\Controllers\TravelOrder;

use App\DTOs\TravelOrderDTO;
use App\Enum\StatusTravelOrderEnum;
use App\Exceptions\TravelOrder\CannotBeCanceledAfterDepartureDateException;
use App\Exceptions\TravelOrder\CannotUpdateSelfTravelOrderException;
use App\Exceptions\TravelOrder\TravelOrderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TravelOrder\AcceptStatusTravelOrderRequest;
use App\Http\Requests\TravelOrder\CancelStatusTravelOrderRequest;
use App\Http\Requests\TravelOrder\StoreTravelOrderRequest;
use App\Http\Resources\TravelOrder\TravelOrderResource;
use App\Interfaces\Services\TravelOrderServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
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

    public function show(int $id): TravelOrderResource | JsonResponse
    {
        try {
            return TravelOrderResource::make($this->travelOrderService->findById($id));
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Travel Order not found!'], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function accept(int $id, AcceptStatusTravelOrderRequest $request): JsonResponse
    {
        try {
            $this->travelOrderService->updateStatus($id, StatusTravelOrderEnum::getNameById($request->input('status_id')));
            return response()->json(['message' => 'Travel Order accepted successfully!'], Response::HTTP_OK);
        } catch (CannotUpdateSelfTravelOrderException) {
            return response()->json(['message' => 'You are not authorized to update the status of your own travel order.'], Response::HTTP_BAD_REQUEST);
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
            return response()->json(['message' => 'You are not authorized to update the status of your own travel order.'], Response::HTTP_BAD_REQUEST);
        } catch (CannotBeCanceledAfterDepartureDateException) {
            return response()->json(['message' => 'A travel order cannot be canceled if the departure date has already passed.'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
