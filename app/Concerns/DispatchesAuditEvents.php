<?php

namespace App\Concerns;

use App\Events\AuditableActionPerformed;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait DispatchesAuditEvents
{
    private array $hiddenAuditFields = [
        'password', 'remember_token', 'email_verification_token',
    ];

    protected function auditCreated(Model $model, ?array $newValues = null): void
    {
        AuditableActionPerformed::dispatch(
            Auth::user(),
            AuditLog::EVENT_CREATED,
            $model,
            null,
            $newValues ?? $this->filterAuditValues($model->getAttributes()),
            request()->ip(),
            request()->userAgent() ?? '',
        );
    }

    protected function auditUpdated(Model $model, array $oldValues, ?array $newValues = null): void
    {
        AuditableActionPerformed::dispatch(
            Auth::user(),
            AuditLog::EVENT_UPDATED,
            $model,
            $oldValues,
            $newValues ?? $this->filterAuditValues($model->getAttributes()),
            request()->ip(),
            request()->userAgent() ?? '',
        );
    }

    protected function auditDeleted(Model $model, ?array $oldValues = null): void
    {
        AuditableActionPerformed::dispatch(
            Auth::user(),
            AuditLog::EVENT_DELETED,
            $model,
            $oldValues ?? $this->filterAuditValues($model->getAttributes()),
            null,
            request()->ip(),
            request()->userAgent() ?? '',
        );
    }

    protected function filterAuditValues(array $values): array
    {
        return array_diff_key($values, array_flip($this->hiddenAuditFields));
    }
}