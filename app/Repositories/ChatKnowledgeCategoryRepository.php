<?php

namespace App\Repositories;

use App\Models\ChatKnowledgeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatKnowledgeCategoryRepository
{
    public function query(): Builder
    {
        return ChatKnowledgeCategory::query();
    }

    public function all(): Collection
    {
        return ChatKnowledgeCategory::orderBy('name')->get(['id', 'name', 'description', 'sort_order']);
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function create(array $data): ChatKnowledgeCategory
    {
        return ChatKnowledgeCategory::create($data);
    }

    public function update(ChatKnowledgeCategory $category, array $data): ChatKnowledgeCategory
    {
        $category->update($data);

        return $category;
    }

    public function delete(ChatKnowledgeCategory $category): void
    {
        $category->delete();
    }
}
