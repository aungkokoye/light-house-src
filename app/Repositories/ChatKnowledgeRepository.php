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
        return ChatKnowledge::query()
            ->select('chat_knowledge.*')
            ->leftJoin('chat_knowledge_categories', 'chat_knowledge_categories.id', '=', 'chat_knowledge.chat_knowledge_category_id')
            ->with('category:id,name');
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return ChatKnowledge::with('category:id,name')
            ->where('active', true)
            ->orderBy('sort_order')
            ->get(['id', 'chat_knowledge_category_id', 'title', 'content']);
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
