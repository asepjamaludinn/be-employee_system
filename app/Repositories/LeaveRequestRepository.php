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
}