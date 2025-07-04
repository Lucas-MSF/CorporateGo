<?php

namespace App\Http\Requests\TravelOrder;

use App\Enum\StatusTravelOrderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusTravelOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id'      => ['required','integer', new Enum(StatusTravelOrderEnum::class)],
        ];
    }
}
