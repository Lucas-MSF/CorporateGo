<?php

namespace App\Http\Requests\TravelOrder;

use App\Enum\StatusTravelOrderEnum;
use App\Http\Requests\TravelOrder\UpdateStatusTravelOrderRequest;

class CancelStatusTravelOrderRequest extends UpdateStatusTravelOrderRequest
{
    protected function prepareForValidation()
    {
        $this->merge(['status_id' => StatusTravelOrderEnum::CANCELED->value]);
    }
}
