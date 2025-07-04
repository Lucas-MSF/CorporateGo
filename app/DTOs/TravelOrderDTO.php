<?php

namespace App\DTOs;

use App\Enum\StatusTravelOrderEnum;
use App\Http\Requests\TravelOrder\StoreTravelOrderRequest;

class TravelOrderDTO
{
        public string $requestorName;
        public string $destination;
        public string $departureDate;
        public string $returnDate;
        public string $status;
        public int $requestorId;

    public function __construct(
        string $requestorName,
        string $destination,
        string $departureDate,
        string $returnDate,
        int $statusId
    ) {
        $this->requestorName = $requestorName;
        $this->destination = $destination;
        $this->departureDate = $departureDate;
        $this->returnDate = $returnDate;
        $this->status = StatusTravelOrderEnum::getNameById($statusId);
        $this->requestorId = auth()->id();
    }

    public function toArray(): array
    {
        return [
            'requestor_name' => $this->requestorName,
            'destination' => $this->destination,
            'departure_date' => $this->departureDate,
            'return_date' => $this->returnDate,
            'status' => $this->status,
            'requestor_id' => $this->requestorId,
        ];
    }

    public static function fromRequest(StoreTravelOrderRequest $request): self
    {
        return new self(
            requestorName: $request->input('requestor_name'),
            destination: $request->input('destination'),
            departureDate: $request->input('departure_date'),
            returnDate: $request->input('return_date'),
            statusId: $request->input('status_id')
        );
    }


}
