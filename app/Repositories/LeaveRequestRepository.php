<?php

namespace App\Repositories;

use App\Models\LeaveRequest;

class LeaveRequestRepository
{
    public function create(array $data)
    {
        return LeaveRequest::create($data);
    }

    public function findById(int $id): LeaveRequest
    {
        return LeaveRequest::findOrFail($id);
    }

    public function update(LeaveRequest $leaveRequest, array $data): LeaveRequest
    {
        $leaveRequest->update($data);
        return $leaveRequest;
    }
    public function getAll()
    {
        return LeaveRequest::with('user:id,name,email,role')->latest()->get();
    }

    public function getByUserId(int $userId)
    {
        return LeaveRequest::where('user_id', $userId)->latest()->get();
    }
}
