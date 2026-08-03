<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\JobListing;
use App\Services\SmartNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $conversations = Conversation::with(['candidate', 'employer', 'job'])
            ->where(fn ($query) => $query->where('candidate_id', $user->id)->orWhere('employer_id', $user->id))
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('chat.index', compact('conversations'));
    }

    public function start(JobListing $job, Request $request): RedirectResponse
    {
        $candidate = $request->user();
        abort_unless($candidate->isCandidate() && $job->is_active, 403);

        $conversation = Conversation::firstOrCreate([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'employer_id' => $job->user_id,
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation, Request $request): View
    {
        $this->authorizeParticipant($conversation, $request->user()->id);
        $conversation->load(['candidate', 'employer', 'job', 'messages.sender']);
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.show', compact('conversation'));
    }

    public function store(Conversation $conversation, Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeParticipant($conversation, $user->id);
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        $conversation->loadMissing(['candidate', 'employer', 'job']);
        $recipient = $conversation->otherParty($user);
        app(SmartNotificationService::class)->create(
            $recipient,
            'chat_message',
            'New message from '.$user->name,
            str($validated['body'])->limit(110),
            route('chat.show', $conversation),
            ['conversation_id' => $conversation->id],
        );

        return back();
    }

    private function authorizeParticipant(Conversation $conversation, int $userId): void
    {
        abort_unless(in_array($userId, [$conversation->candidate_id, $conversation->employer_id], true), 403);
    }
}
