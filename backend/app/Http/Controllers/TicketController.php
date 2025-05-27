<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
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
