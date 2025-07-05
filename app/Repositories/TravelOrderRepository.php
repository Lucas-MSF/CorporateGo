<?php

namespace App\Repositories;

use App\Interfaces\Repositories\TravelOrderRepositoryInterface;
use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Collection;

class TravelOrderRepository implements TravelOrderRepositoryInterface
{
    public function __construct(private readonly TravelOrder $model)
    {
    }

    public function create(array $data): TravelOrder
    {
        return $this->model->create($data);
    }

    public function updateStatus(int $travelOrderId, string $status): void
    {
        $this->model->newQueryWithoutScopes()->where('id', $travelOrderId)->update(['status' => $status]);
    }

    public function finByIdWithoutScopes(int $travelOrderId): TravelOrder
    {
        return $this->model->newQueryWithoutScopes()->find($travelOrderId);
    }

    public function findById(int $travelOrderId): TravelOrder
    {
        return $this->model->query()->findOrFail($travelOrderId);
    }

    public function getAll(array $filters): Collection
    {
        return $this->model->query()
            ->when(!empty($filters), function ($query) use ($filters) {
                foreach ($filters as $key => $value) {
                    match ($key) {
                        'start_date' => $query->whereDate('departure_date', '>=', $value),
                        'end_date'   => $query->whereDate('departure_date', '<=', $value),
                        default      => $query->where($key, $value),
                    };
                }
            })
            ->get();
    }
}
