<?php

namespace App\Repositories;

use App\Models\ChatKnowledge;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatKnowledgeRepository
{
    public function query(): Builder
    {
        return ChatKnowledge::query();
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return ChatKnowledge::where('active', true)
            ->orderBy('sort_order')
            ->orderBy('category')
            ->get(['category', 'title', 'content']);
    }

    public function create(array $data): ChatKnowledge
    {
        return ChatKnowledge::create($data);
    }

    public function update(ChatKnowledge $knowledge, array $data): ChatKnowledge
    {
        $knowledge->update($data);

        return $knowledge;
    }

    public function delete(ChatKnowledge $knowledge): void
    {
        $knowledge->delete();
    }
}
