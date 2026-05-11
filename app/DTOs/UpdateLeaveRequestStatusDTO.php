<?php

namespace App\DTOs;

class UpdateLeaveRequestStatusDTO
{
    public function __construct(
        public readonly int $leaveRequestId,
        public readonly string $status
    ) {}
}