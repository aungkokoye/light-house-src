<?php

namespace App\Concerns;

use App\Concerns\DispatchesAuditEvents;

trait AuditableCrud
{
    use DispatchesAuditEvents;
}