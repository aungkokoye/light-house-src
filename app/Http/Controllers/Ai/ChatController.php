<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Services\ChatKnowledgeManager;
use Illuminate\Http\Request;
use OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(private readonly ChatKnowledgeManager $knowledgeManager) {}

    public function stream(Request $request): StreamedResponse
    {
        $this->authorize('chat.stream');

        $request->validate([
            'message'           => ['required', 'string', 'max:2000'],
            'history'           => ['array', 'max:20'],
            'history.*.role'    => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $client = OpenAI::factory()
            ->withApiKey(config('ai.api_key'))
            ->withBaseUri(config('ai.base_uri'))
            ->make();

        $messages = array_merge(
            [['role' => 'system', 'content' => $this->knowledgeManager->buildSystemPrompt()]],
            $request->input('history', []),
            [['role' => 'user', 'content' => $request->input('message')]],
        );

        $stream = $client->chat()->createStreamed([
            'model'    => config('ai.model'),
            'messages' => $messages,
        ]);

        return response()->stream(function () use ($stream) {
            // Clear all output buffers so Apache doesn't buffer the stream
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            foreach ($stream as $response) {
                $delta = $response->choices[0]->delta->content ?? '';
                if ($delta !== '') {
                    echo 'data: ' . json_encode(['content' => $delta]) . "\n\n";
                    flush();
                }
            }

            echo "data: [DONE]\n\n";
            flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
