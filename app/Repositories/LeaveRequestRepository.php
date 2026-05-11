<?php

namespace App\Repositories;

use App\Models\LeaveRequest;

class LeaveRequestRepository
{
    public function create(array $data)
    {
        return LeaveRequest::create($data);
    }
}