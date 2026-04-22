<?php

namespace App\Events\Contracts;

interface AuditableEvent
{
    public function getEvent(): string;
    public function getUserId(): ?int;
    public function getUserEmail(): ?string;
    public function getAuditableType(): ?string;
    public function getAuditableId(): ?int;
    public function getOldValues(): ?array;
    public function getNewValues(): ?array;
    public function getIpAddress(): string;
    public function getUserAgent(): string;
}