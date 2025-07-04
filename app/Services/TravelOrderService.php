<?php

namespace App\Services;

use App\DTOs\TravelOrderDTO;
use App\Exceptions\TravelOrder\CannotUpdateSelfTravelOrderException;
use App\Interfaces\Repositories\TravelOrderRepositoryInterface;
use App\Interfaces\Services\TravelOrderServiceInterface;
use App\Models\TravelOrder;

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
        $this->checkUserIdAndRequestorId($travelOrderId);
        $this->travelOrderRepository->updateStatus($travelOrderId, $status);
    }

    private function checkUserIdAndRequestorId(int $travelOderId): void
    {
        $travelOrder = $this->travelOrderRepository->findById($travelOderId);
        throw_if(
            $travelOrder->requestor_id === auth()->id(),
            CannotUpdateSelfTravelOrderException::class
        );
    }
}
