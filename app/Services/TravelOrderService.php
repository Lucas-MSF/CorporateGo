<?php

namespace App\Services;

use App\DTOs\TravelOrderDTO;
use App\Enum\StatusTravelOrderEnum;
use App\Events\TravelOrder\TravelOrderStatusChangedEvent;
use App\Exceptions\TravelOrder\CannotBeCanceledAfterDepartureDateException;
use App\Exceptions\TravelOrder\CannotUpdateSelfTravelOrderException;
use App\Interfaces\Repositories\TravelOrderRepositoryInterface;
use App\Interfaces\Services\TravelOrderServiceInterface;
use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class TravelOrderService implements TravelOrderServiceInterface
{
    public function __construct(private readonly TravelOrderRepositoryInterface $travelOrderRepository)
    {
    }

    public function create(TravelOrderDTO $dto): TravelOrder
    {
        return $this->travelOrderRepository->create($dto->toArray());
    }

    public function updateStatus(int $travelOrderId, string $status): void
    {
        $travelOrder = $this->getTravelOrder($travelOrderId);
        $this->checkUserIdAndRequestorId($travelOrder);
        $this->checkCanBeCancelled($travelOrder, $status);
        $this->travelOrderRepository->updateStatus($travelOrderId, $status);
        event(new TravelOrderStatusChangedEvent($travelOrder->fresh()));
    }

    private function getTravelOrder(int $travelOrderId): TravelOrder
    {
        return $this->travelOrderRepository->finByIdWithoutScopes($travelOrderId);
    }

    private function checkUserIdAndRequestorId(TravelOrder $travelOrder): void
    {
        throw_if(
            $travelOrder->requestor_id === auth()->id(),
            CannotUpdateSelfTravelOrderException::class
        );
    }

    private function checkCanBeCancelled(TravelOrder $travelOrder, string $status): void
    {
        throw_if(
            $status === StatusTravelOrderEnum::CANCELED->label() &&
            Carbon::parse($travelOrder->departure_date)->lt(Carbon::today()),
            CannotBeCanceledAfterDepartureDateException::class
        );
    }

    public function findById(int $travelOrderId): TravelOrder
    {
        return $this->travelOrderRepository->findById($travelOrderId);
    }

    public function getAll(array $filters): Collection
    {
        return $this->travelOrderRepository->getAll($filters);
    }
}
