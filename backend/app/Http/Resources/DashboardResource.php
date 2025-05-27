<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_tickets' => $this['total_tickets'] ?? 0,
            'active_tickets' => $this['active_tickets'] ?? 0,
            'resolved_tickets' => $this['resolved_tickets'] ?? 0,
            'avg_resolution_time' => number_format($this['avg_resolution_time'], 2),
            'status_distribution' => [
                'open' => $this['status_distribution']['open'] ?? 0,
                'onprogress' => $this['status_distribution']['onprogress'] ?? 0,
                'resolved' => $this['status_distribution']['resolved'] ?? 0,
                'closed' => $this['status_distribution']['closed'] ?? 0,
            ],
        ];
    }
}
