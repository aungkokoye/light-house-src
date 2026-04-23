<?php

namespace App\Services;

use App\Concerns\DispatchesAuditEvents;
use App\Filters\ChatKnowledgeCategoryFilter;
use App\Models\ChatKnowledgeCategory;
use App\Repositories\ChatKnowledgeCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ChatKnowledgeCategoryManager
{
    use DispatchesAuditEvents;

    const array PER_PAGE_LIST    = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly ChatKnowledgeCategoryRepository $repo) {}

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = ChatKnowledgeCategoryFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query();

        return $this->repo->paginate($query, $perPage);
    }

    public function show(ChatKnowledgeCategory $category): ChatKnowledgeCategory
    {
        return $category->load(['createdBy:id,name,email']);
    }

    public function create(array $data): ChatKnowledgeCategory
    {
        $category = $this->repo->create([...$data, 'created_by' => Auth::id()]);
        $this->auditCreated($category);

        return $category->load(['createdBy:id,name,email']);
    }

    public function update(ChatKnowledgeCategory $category, array $data): ChatKnowledgeCategory
    {
        $oldValues = $this->filterAuditValues($category->getAttributes());
        $category  = $this->repo->update($category, $data);
        $this->auditUpdated($category, $oldValues);

        return $category->load(['createdBy:id,name,email']);
    }

    public function delete(ChatKnowledgeCategory $category): void
    {
        $this->auditDeleted($category);
        $this->repo->delete($category);
    }
}
