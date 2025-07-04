<?php

namespace App\Http\Requests\TravelOrder;

use App\Enum\StatusTravelOrderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTravelOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requestor_name' => ['required','string'],
            'destination'    => ['required','string'],
            'departure_date' => ['required', 'date_format:Y-m-d','after_or_equal:today'],
            'return_date'    => ['required', 'date_format:Y-m-d','after_or_equal:departure_date'],
            'status_id'      => ['required','integer', new Enum(StatusTravelOrderEnum::class)],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['status_id' => StatusTravelOrderEnum::PENDING->value]);
    }
}
