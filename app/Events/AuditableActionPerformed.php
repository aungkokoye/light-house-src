<?php

namespace App\Events;

use App\Events\Contracts\AuditableEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class AuditableActionPerformed implements AuditableEvent
{
    use Dispatchable;

    public function __construct(
        public readonly ?User $user,
        public readonly string $event,
        public readonly Model $auditable,
        public readonly ?array $oldValues,
        public readonly ?array $newValues,
        public readonly string $ipAddress,
        public readonly string $userAgent,
    ) {}

    public function getEvent(): string          { return $this->event; }
    public function getUserId(): ?int           { return $this->user?->id; }
    public function getUserEmail(): ?string     { return $this->user?->email; }
    public function getAuditableType(): ?string { return get_class($this->auditable); }
    public function getAuditableId(): ?int      { return $this->auditable->getKey(); }
    public function getOldValues(): ?array      { return $this->oldValues; }
    public function getNewValues(): ?array      { return $this->newValues; }
    public function getIpAddress(): string      { return $this->ipAddress; }
    public function getUserAgent(): string      { return $this->userAgent; }
}