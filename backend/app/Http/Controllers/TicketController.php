<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    private function applyFilters(Request $request)
    {
        $query = Ticket::query()
            ->select('id', 'title', 'description', 'code', 'status', 'priority', 'user_id', 'created_at', 'updated_at')
            ->when(auth()->user()->role != 'admin', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->priority, function ($q, $priority) {
                $q->where('priority', $priority);
            })
            ->latest();

        return $query->get();
    }

    public function index(Request $request)
    {
        try {
            $tickets = $this->applyFilters($request);

            return response()->json([
                'message' => 'Tickets retrieved successfully',
                'data' => TicketResource::collection($tickets)
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Failed to retrieve tickets',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function store(TicketRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $ticket = auth()->user()->tickets()->create([
                'code' => strtoupper('TIC-' . rand(10000, 99990)),
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Ticket created successfully',
                'ticket' => new TicketResource($ticket)
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create ticket',
                'error' => $err->getMessage()
            ], 500);
        }
    }
}
