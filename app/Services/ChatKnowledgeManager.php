<?php

namespace App\Services;

use App\Concerns\DispatchesAuditEvents;
use App\Filters\ChatKnowledgeFilter;
use App\Models\ChatKnowledge;
use App\Repositories\ChatKnowledgeRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ChatKnowledgeManager
{
    use DispatchesAuditEvents;

    const array PER_PAGE_LIST    = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly ChatKnowledgeRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = ChatKnowledgeFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->category($request->input('category'))
            ->active($request->input('active'))
            ->sort($request->input('sort_by', 'sort_order'), $request->input('sort_dir', 'asc'))
            ->query();

        return $this->repo->paginate($query, $perPage);
    }

    public function show(ChatKnowledge $knowledge): ChatKnowledge
    {
        return $knowledge->load(['createdBy:id,name,email']);
    }

    public function create(array $data): ChatKnowledge
    {
        $knowledge = $this->repo->create([...$data, 'created_by' => Auth::id()]);
        $this->auditCreated($knowledge);

        return $knowledge->load(['createdBy:id,name,email']);
    }

    public function update(ChatKnowledge $knowledge, array $data): ChatKnowledge
    {
        $oldValues = $this->filterAuditValues($knowledge->getAttributes());
        $knowledge = $this->repo->update($knowledge, $data);
        $this->auditUpdated($knowledge, $oldValues);

        return $knowledge->load(['createdBy:id,name,email']);
    }

    public function delete(ChatKnowledge $knowledge): void
    {
        $this->auditDeleted($knowledge);
        $this->repo->delete($knowledge);
    }

    public function buildSystemPrompt(): string
    {
        $entries = $this->repo->allActive();

        if ($entries->isEmpty()) {
            $phone    = config('contact.phone');
            $email    = config('contact.email');
            $messenger = config('contact.messenger_name');

            return "You are a helpful customer support assistant for LightHouse Printing, Myanmar.\nIf you don't know the answer, politely say so and provide contact details:\n- Phone: $phone\n- Email: $email\n- Messenger: $messenger";
        }

        $sections = $entries
            ->groupBy(fn ($item) => $item->category?->name ?? 'General')
            ->map(fn ($items, $category) => "## {$category}\n" .
                $items->map(fn ($item) => "### {$item->title}\n{$item->content}")->implode("\n\n")
            )
            ->implode("\n\n");

        $phone     = config('contact.phone');
        $email     = config('contact.email');
        $messenger = config('contact.messenger_name');

        return <<<PROMPT
You are a helpful customer support assistant for LightHouse Printing, Myanmar.

Use the knowledge base below to answer customer questions accurately.
Be concise, friendly, and professional.
Respond in the same language the customer uses (English or Myanmar).

If the answer is not in the knowledge base, respond politely with something like:
"I'm sorry, I don't have that information. Please contact us directly and our team will be happy to help:
- Phone: $phone
- Email: $email
- Messenger: $messenger"

Do NOT make up information. Only answer based on the knowledge base provided.

---

$sections
PROMPT;
    }
}
