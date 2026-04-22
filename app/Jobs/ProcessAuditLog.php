<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAuditLog implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 5;
    public int $uniqueFor = 10;

    public function __construct(
        public readonly ?int $userId,
        public readonly ?string $userEmail,
        public readonly string $event,
        public readonly ?string $auditableType,
        public readonly ?int $auditableId,
        public readonly ?array $oldValues,
        public readonly ?array $newValues,
        public readonly string $ipAddress,
        public readonly string $userAgent,
    ) {}

    public function uniqueId(): string
    {
        return md5($this->userId . $this->event . $this->ipAddress . floor(time() / 10));
    }

    public function handle(): void
    {
        AuditLog::create([
            'user_id'        => $this->userId,
            'user_email'     => $this->userEmail,
            'event'          => $this->event,
            'auditable_type' => $this->auditableType,
            'auditable_id'   => $this->auditableId,
            'old_values'     => $this->oldValues,
            'new_values'     => $this->newValues,
            'ip_address'     => $this->ipAddress,
            'user_agent'     => $this->userAgent,
        ]);
    }
}