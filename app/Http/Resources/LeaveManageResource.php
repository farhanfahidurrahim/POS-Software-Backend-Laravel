<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveManageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->employee),
            'leave' => new LeaveResource($this->leave),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_leave' => $this->total_leave,
            'document' =>config('app.url')."/". config('imagepath.leaveManage')."/".$this->document,
            'reason' => $this->reason,
            'status' => $this->status,
        ];
    }
}
