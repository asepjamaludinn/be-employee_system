<?php

namespace App\Services;

use App\Repositories\LeaveRequestRepository;
use App\DTOs\CreateLeaveRequestDTO;
use App\DTOs\UpdateLeaveRequestStatusDTO;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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

    public function updateStatus(UpdateLeaveRequestStatusDTO $data)
    {
        return DB::transaction(function () use ($data) {
            $leaveRequest = $this->leaveRequestRepository->findById($data->leaveRequestId);

            if ($leaveRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => ['Pengajuan ini sudah diproses dan tidak dapat diubah lagi.']
                ]);
            }

            if ($data->status === 'approved') {
                $user = $leaveRequest->user;
                $start = Carbon::parse($leaveRequest->start_date);
                $end = Carbon::parse($leaveRequest->end_date);
                $requestedDays = $start->diffInDays($end) + 1;

                if ($user->leave_quota < $requestedDays) {
                    throw ValidationException::withMessages([
                        'leave_quota' => ['Sisa kuota cuti karyawan tidak mencukupi untuk disetujui.']
                    ]);
                }

                $user->decrement('leave_quota', $requestedDays);
            }

            return $this->leaveRequestRepository->update($leaveRequest, [
                'status' => $data->status
            ]);
        });
    }
    public function getLeaveRequests($user)
    {
        if ($user->role === 'admin') {
            return $this->leaveRequestRepository->getAll();
        }

        return $this->leaveRequestRepository->getByUserId($user->id);
    }
}