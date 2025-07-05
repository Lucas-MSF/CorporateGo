<?php

namespace App\Interfaces\Services;

use App\DTOs\TravelOrderDTO;
use App\Models\TravelOrder;

interface TravelOrderServiceInterface
{
    public function create(TravelOrderDTO $dto): TravelOrder;

    public function updateStatus(int $travelOrderId, string $status): void;

    public function findById(int $travelOrderId): TravelOrder;
}
