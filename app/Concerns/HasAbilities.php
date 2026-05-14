<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Gate;

trait HasAbilities
{
    protected function listAbilities(string $model): array
    {
        $user     = auth()->user();
        $policy   = Gate::getPolicyFor($model);
        $instance = new $model();

        return [
            'create' => $policy ? $policy->create($user) : false,
            'view'   => $policy ? $policy->view($user, $instance) : false,
            'edit'   => $policy ? $policy->update($user, $instance) : false,
            'delete' => $policy ? $policy->delete($user, $instance) : false,
        ];
    }

    protected function resourceAbilities(mixed $model): array
    {
        $user   = auth()->user();
        $policy = Gate::getPolicyFor($model);

        return [
            'edit'   => $policy ? $policy->update($user, $model) : false,
            'delete' => $policy ? $policy->delete($user, $model) : false,
        ];
    }
}
