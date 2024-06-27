<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "employee" => new EmployeeResource($this->employee),
            "attendance_count" => $this->attendance_count,
            "leave_count" => $this->leave_count,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "total_amount" => $this->total_amount,
            "partial_payment" => $this->partial_payment,
            "due_payment" => $this->due_payment,
            "payment_status" => $this->payment_status,
        ];
    }
}
