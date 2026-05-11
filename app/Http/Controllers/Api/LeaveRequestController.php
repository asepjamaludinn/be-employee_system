<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeaveRequestService;
use App\DTOs\CreateLeaveRequestDTO;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function __construct(
        protected LeaveRequestService $leaveRequestService
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048', 
        ]);

        $dto = new CreateLeaveRequestDTO(
            $request->user(), 
            $request->start_date,
            $request->end_date,
            $request->reason,
            $request->file('attachment')
        );

        $result = $this->leaveRequestService->createRequest($dto);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dibuat dan menunggu persetujuan.',
            'data' => $result
        ], 201);
    }
}