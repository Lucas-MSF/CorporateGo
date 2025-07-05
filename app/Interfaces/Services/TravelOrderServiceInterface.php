<?php

namespace App\Interfaces\Services;

use App\DTOs\TravelOrderDTO;
use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TravelOrderServiceInterface
{
    public function create(TravelOrderDTO $dto): TravelOrder;

    public function updateStatus(int $travelOrderId, string $status): void;

    public function findById(int $travelOrderId): TravelOrder;

    public function getAll(array $filters): Collection;
}
