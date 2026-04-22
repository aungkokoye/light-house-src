<?php

namespace App\Listeners;

use App\Events\Contracts\AuditableEvent;
use App\Jobs\ProcessAuditLog;

class DispatchAuditLog
{
    public function handle(AuditableEvent $event): void
    {
        ProcessAuditLog::dispatch(
            userId:        $event->getUserId(),
            userEmail:     $event->getUserEmail(),
            event:         $event->getEvent(),
            auditableType: $event->getAuditableType(),
            auditableId:   $event->getAuditableId(),
            oldValues:     $event->getOldValues(),
            newValues:     $event->getNewValues(),
            ipAddress:     $event->getIpAddress(),
            userAgent:     $event->getUserAgent(),
        );
    }
}