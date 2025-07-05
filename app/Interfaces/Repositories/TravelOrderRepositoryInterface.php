<?php

namespace App\Interfaces\Repositories;

use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Collection;

interface TravelOrderRepositoryInterface
{
    public function create(array $data): TravelOrder;

    public function updateStatus(int $travelOrderId, string $status): void;

    public function finByIdWithoutScopes(int $travelOrderId): TravelOrder;

    public function findById(int $travelOrderId): TravelOrder;

    public function getAll(array $filters): Collection;
}
