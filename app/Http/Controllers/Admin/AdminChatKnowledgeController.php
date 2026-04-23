<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChatKnowledgeRequest;
use App\Http\Requests\Admin\UpdateChatKnowledgeRequest;
use App\Models\ChatKnowledge;
use App\Services\ChatKnowledgeManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminChatKnowledgeController extends Controller
{
    public function __construct(private readonly ChatKnowledgeManager $manager) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ChatKnowledge::class);

        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->manager->list($request, $perPage));
    }

    public function store(StoreChatKnowledgeRequest $request): JsonResponse
    {
        $this->authorize('create', ChatKnowledge::class);

        return response()->json($this->manager->create($request->validated()), 201);
    }

    public function show(ChatKnowledge $chatKnowledge): JsonResponse
    {
        $this->authorize('view', $chatKnowledge);

        return response()->json($this->manager->show($chatKnowledge));
    }

    public function update(UpdateChatKnowledgeRequest $request, ChatKnowledge $chatKnowledge): JsonResponse
    {
        $this->authorize('update', $chatKnowledge);

        return response()->json($this->manager->update($chatKnowledge, $request->validated()));
    }

    public function destroy(ChatKnowledge $chatKnowledge): JsonResponse
    {
        $this->authorize('delete', $chatKnowledge);

        $this->manager->delete($chatKnowledge);

        return response()->json(['message' => 'Deleted.']);
    }
}
