<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketReplyRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\TicketReplyResource;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    private function applyFilters(Request $request)
    {
        $query = Ticket::query()
            ->with('replies')
            ->select('id', 'title', 'description', 'code', 'status', 'priority', 'user_id', 'created_at', 'updated_at', 'completed_at')
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
                'data' => TicketResource::collection($tickets->load('replies.user'))
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Failed to retrieve tickets',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function show(string $code)
    {
        try {
            $ticket = Ticket::with('replies')->where('code', $code)->first();

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Ensure the user has access to the ticket
            if (auth()->user()->role != 'admin' && $ticket->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized access to this ticket'
                ], 403);
            }

            return response()->json([
                'message' => 'Ticket retrieved successfully',
                'data' => new TicketResource($ticket)
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Failed to retrieve single ticket',
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

    public function storeReply(TicketReplyRequest $request, string $code)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $ticket = Ticket::where('code', $code)->first();

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Ensure the user has access to the ticket
            if (auth()->user()->role != 'admin' && $ticket->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized access to this ticket'
                ], 403);
            }

            $reply = $ticket->replies()->create([
                'user_id' => auth()->user()->id,
                'content' => $data['content'],
            ]);

            // Update ticket status if admin
            if (auth()->user()->role === 'admin' && isset($data['status'])) {
                $ticket->update(['status' => $data['status']]);
                // If the status is resolved, set completed_at to now, otherwise null
                if ($data['status'] == 'resolved') {
                    $ticket->update(['completed_at' => now()]);
                } else {
                    $ticket->update(['completed_at' => null]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Reply added successfully',
                'reply' => new TicketReplyResource($reply),
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add reply',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function destroy(string $code)
    {
        try {
            $ticket = Ticket::where('code', $code)->first();

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Ensure the user has access to the ticket
            if (auth()->user()->role != 'admin' && $ticket->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized access to this ticket'
                ], 403);
            }

            $ticket->delete();

            return response()->json([
                'message' => 'Ticket deleted successfully'
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Failed to delete ticket',
                'error' => $err->getMessage()
            ], 500);
        }
    }   
}
