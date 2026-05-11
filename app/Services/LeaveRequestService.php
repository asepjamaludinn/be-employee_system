<?php

namespace App\Services;

use App\Repositories\LeaveRequestRepository;
use App\DTOs\CreateLeaveRequestDTO;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LeaveRequestService
{
    public function __construct(
        protected LeaveRequestRepository $leaveRequestRepository
    ) {}

    public function createRequest(CreateLeaveRequestDTO $data)
    {

        $start = Carbon::parse($data->startDate);
        $end = Carbon::parse($data->endDate);
        
        if ($end->isBefore($start)) {
            throw ValidationException::withMessages([
                'end_date' => ['Tanggal selesai tidak boleh lebih awal dari tanggal mulai.']
            ]);
        }

        $requestedDays = $start->diffInDays($end) + 1;

        if ($requestedDays > $data->user->leave_quota) {
            throw ValidationException::withMessages([
                'leave_quota' => ["Kuota cuti Anda tidak mencukupi. Sisa kuota: {$data->user->leave_quota} hari, Pengajuan: {$requestedDays} hari."]
            ]);
        }

        $attachmentPath = null;
        if ($data->attachment) {
            $attachmentPath = $data->attachment->store('leave_attachments', 'public');
        }

        return $this->leaveRequestRepository->create([
            'user_id' => $data->user->id,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'reason' => $data->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending' 
        ]);
    }
}