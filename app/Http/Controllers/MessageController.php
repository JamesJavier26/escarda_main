<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use function App\Helpers\notify_user;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $conversationId = null)
    {
        $conversation = null;

        if ($conversationId) {
            $conversation = Conversation::where('id', $conversationId)
                ->whereHas('users', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->with([
                    'users:id,username',
                    'lastMessage.sender:id,username',
                ])
                ->first();

            if (!$conversation) {
                \Log::error('[Chat] Conversation not found or user not a participant', [
                    'conversation_id' => $conversationId,
                    'user_id' => auth()->id(),
                ]);
                abort(404, 'Conversation not found.');
            }
        }

        $conversations = Conversation::whereHas('users', fn ($q) =>
                $q->where('user_id', auth()->id()))
            ->with([
                'users:id,username',
                'lastMessage.sender:id,username',
            ])
            ->latest('updated_at')
            ->get();

        $allUsers = User::select('id', 'username')->where('id', '!=', auth()->id())->get();

        return view('messages.index', [
            'conversation' => $conversation,
            'conversations' => $conversations,
            'allUsers' => $allUsers,
        ]);
    }

    public function fetch(Conversation $conversation)
    {
        return $conversation->messages()
            ->with('sender:id,username')
            ->latest('id')
            ->take(50)
            ->get()
            ->reverse()      // chronological
            ->values();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'body'       => 'required_without:attachment|max:5000',
            'attachment' => 'nullable|file|max:20480',
        ]);

        // simple file handling (tweak later)
        if ($request->hasFile('attachment')) {
            $data['attachment']    = $request->file('attachment')->store('chat', 'public');
            $data['message_type']  = str_starts_with($request->file('attachment')->getMimeType(), 'image')
                                      ? 'image' : 'file';
        }

        $message = $conversation->messages()->create([
            'user_id'      => $request->user()->id,
            'body'         => $data['body'] ?? null,
            'attachment'   => $data['attachment'] ?? null,
            'message_type' => $data['message_type'] ?? 'text',
        ]);

        broadcast(new MessageSent($message))->toOthers();
        // notify_user(1, 'New Report Uploaded', 'Check the attached file.', [
        //     'attachment' => '/storage/reports/report_123.pdf',
        //     'action_button' => [
        //         'label' => 'View Report',
        //         'url' => '/reports/123'
        //     ]
        // ]);
        $recipientIds = $message->conversation
            ->users()
            ->where('users.id', '!=', $message->sender_id)
            ->pluck('users.id');

        foreach ($recipientIds as $id) {
            notify_user($id, 'Message from ' . $message->sender->username, $message->body ?? 'Sent a file.', [
                'attachment' => $message->attachment ? '/storage/' . $message->attachment : null,
                'action_button' => [
                    'label' => 'Open Chat',
                    'url' => route('messages.index', $message->conversation_id)
                ]
            ]);
        }


        // return to sender so Alpine can append instantly
        return $message->load('sender:id,username');
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }

public function startDirect(Request $request)
{
    $authId = auth()->id();
    $userId = $request->user_id;

    // Log input for debug
    \Log::info('[startDirect] Attempting direct chat', [
        'auth_id' => $authId,
        'user_id' => $userId,
    ]);

    $conversation = Conversation::where('type', 'direct')
        ->whereHas('users', fn ($q) => $q->where('user_id', $authId))
        ->whereHas('users', fn ($q) => $q->where('user_id', $userId))
        ->first();

    if (!$conversation) {
        $conversation = Conversation::create(['type' => 'direct']);
        $conversation->users()->attach([$authId, $userId]);

        \Log::info('[startDirect] New direct conversation created', [
            'conversation_id' => $conversation->id
        ]);
    }

    return response()->json([
        'url' => route('messages.index', ['conversationId' => $conversation->id]),
        'id' => $conversation->id,
    ]);
}

public function createGroup(Request $request)
{
    $data = $request->validate([
        'user_ids' => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
        'name' => 'nullable|string|max:255',
    ]);

    $authId = auth()->id();

    \Log::info('[createGroup] Creating group chat', [
        'auth_id' => $authId,
        'user_ids' => $request->user_ids,
    ]);

    // Auto-generate name if not provided
    if (empty($data['name'])) {
        $usernames = User::whereIn('id', $data['user_ids'])->pluck('username')->toArray();

        $data['name'] = count($usernames) === 1
            ? 'Group with ' . $usernames[0]
            : 'Group with ' . implode(', ', array_slice($usernames, 0, -1)) . ' and ' . end($usernames);
    }

    $conversation = Conversation::create([
        'type' => 'group',
        'name' => $data['name'],
        'created_by' => $authId,
    ]);

    $conversation->users()->attach(array_merge([$authId], $request->user_ids));

    \Log::info('[createGroup] Group conversation created', [
        'conversation_id' => $conversation->id
    ]);

    return response()->json([
        'url' => route('messages.index', ['conversationId' => $conversation->id]),
        'id' => $conversation->id,
    ]);
}

public function leave(Conversation $conversation)
{
    $conversation->users()->detach(auth()->id());

    if ($conversation->users()->count() === 0) {
        $conversation->delete(); // Optional: Delete if empty
    }

    return redirect()->route('messages.index')->with('status', 'You left the chat.');
}

public function members(Conversation $conversation)
{
    return response()->json($conversation->users()->select('id', 'username')->get());
}

public function manageUsers(Request $request, Conversation $conversation)
{
    $data = $request->validate([
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id',
    ]);

    $conversation->users()->sync(array_unique(array_merge([$conversation->created_by], $data['user_ids'])));

    return response()->json(['message' => 'Updated group members']);
}


}
