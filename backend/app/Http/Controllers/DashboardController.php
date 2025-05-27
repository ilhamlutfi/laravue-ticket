<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Total number of tickets created in the current month
        $totalTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
        ->count();

        // Total number of active tickets in the current month
        $activeTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
            ->where('status', '!=', 'resolved')
            ->count();

        // Total number of resolved tickets in the current month
        $resolvedTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
            ->where('status', 'resolved')
            ->count();

        $avgResolutionTime = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
            ->where('status', 'resolved')
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_time'))
            ->value('avg_time') ?? 0;

        $statusDistribution = [
            'open' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'open')
                ->count(),
            'onprogress' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'onprogress')
                ->count(),
            'resolved' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'resolved')
                ->count(),
            'closed' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'closed')
                ->count(),
        ];

        $dashboardData = [
            'total_tickets' => $totalTickets,
            'active_tickets' => $activeTickets,
            'resolved_tickets' => $resolvedTickets,
            'avg_resolution_time' => $avgResolutionTime,
            'status_distribution' => $statusDistribution,
        ];

        return response()->json([
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => new DashboardResource($dashboardData)
        ], 200);
    }
}
