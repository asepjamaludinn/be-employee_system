<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;
use App\Models\User;

class CreateLeaveRequestDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $reason,
        public readonly ?UploadedFile $attachment
    ) {}
}