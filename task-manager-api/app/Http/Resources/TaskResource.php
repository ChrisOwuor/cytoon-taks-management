<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'deadline' => $this->deadline,
            'assigned_to' => $this->assignee?->name,
            'created_by' => $this->creator?->name,
            'assigned_by' => $this->assigner?->name,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
        ];
    }
}
