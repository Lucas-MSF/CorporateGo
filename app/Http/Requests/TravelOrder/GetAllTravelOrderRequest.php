<?php

namespace App\Http\Requests\TravelOrder;

use App\Enum\StatusTravelOrderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetAllTravelOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
    {
        if($this['status_id']) {
            $this->merge(['status' => StatusTravelOrderEnum::getNameById($this['status_id'])]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'integer', new Enum(StatusTravelOrderEnum::class)],
            'status' => ['nullable', 'string'],
            'destination' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_with:start_date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace(
            collect($this->validated())->except('status_id')->toArray()
        );
    }
}
