<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChatKnowledgeCategoryRequest;
use App\Http\Requests\Admin\UpdateChatKnowledgeCategoryRequest;
use App\Models\ChatKnowledgeCategory;
use App\Services\ChatKnowledgeCategoryManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminChatKnowledgeCategoryController extends Controller
{
    public function __construct(private readonly ChatKnowledgeCategoryManager $manager) {}

    public function all(): JsonResponse
    {
        $this->authorize('viewAny', ChatKnowledgeCategory::class);

        return response()->json($this->manager->all());
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ChatKnowledgeCategory::class);

        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->manager->list($request, $perPage));
    }

    public function store(StoreChatKnowledgeCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', ChatKnowledgeCategory::class);

        return response()->json($this->manager->create($request->validated()), 201);
    }

    public function show(ChatKnowledgeCategory $chatKnowledgeCategory): JsonResponse
    {
        $this->authorize('view', $chatKnowledgeCategory);

        return response()->json($this->manager->show($chatKnowledgeCategory));
    }

    public function update(UpdateChatKnowledgeCategoryRequest $request, ChatKnowledgeCategory $chatKnowledgeCategory): JsonResponse
    {
        $this->authorize('update', $chatKnowledgeCategory);

        return response()->json($this->manager->update($chatKnowledgeCategory, $request->validated()));
    }

    public function destroy(ChatKnowledgeCategory $chatKnowledgeCategory): JsonResponse
    {
        $this->authorize('delete', $chatKnowledgeCategory);

        $this->manager->delete($chatKnowledgeCategory);

        return response()->json(['message' => 'Deleted.']);
    }
}
