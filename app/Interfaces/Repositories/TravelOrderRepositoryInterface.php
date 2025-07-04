<?php

namespace App\Interfaces\Repositories;

use App\Models\TravelOrder;

interface TravelOrderRepositoryInterface
{
    public function create(array $data): TravelOrder;

    public function updateStatus(int $travelOrderId, string $status): void;

    public function findById(int $travelOrderId): TravelOrder;
}
