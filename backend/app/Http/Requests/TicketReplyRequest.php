<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'status' => auth()->user()->role === 'admin' ? 'required|in:open,onprogress,resolved,rejected' : 'nullable' // Admins can set status, users cannot
        ];
    }
}
