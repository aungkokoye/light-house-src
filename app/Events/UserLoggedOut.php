<?php

namespace App\Events;

use App\Events\Contracts\AuditableEvent;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class UserLoggedOut implements AuditableEvent
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress,
        public readonly string $userAgent,
    ) {}

    public function getEvent(): string        { return AuditLog::EVENT_LOGOUT; }
    public function getUserId(): ?int         { return $this->user->id; }
    public function getUserEmail(): ?string   { return $this->user->email; }
    public function getAuditableType(): ?string { return null; }
    public function getAuditableId(): ?int    { return null; }
    public function getOldValues(): ?array    { return null; }
    public function getNewValues(): ?array    { return null; }
    public function getIpAddress(): string    { return $this->ipAddress; }
    public function getUserAgent(): string    { return $this->userAgent; }
}