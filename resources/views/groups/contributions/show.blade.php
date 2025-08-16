<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $contribution->title }}</h2>
    </x-slot>

    @can('update', $contribution)
    <div class="flex gap-2">
        <a class="px-3 py-2 rounded bg-blue-600 text-white" href="{{ route('groups.contributions.edit', [$group, $contribution]) }}">Edit</a>

        <form method="POST" action="{{ route('groups.contributions.destroy', [$group, $contribution]) }}"
              onsubmit="return confirm('Delete this contribution?');">
            @csrf
            @method('DELETE')
            <button class="px-3 py-2 rounded bg-red-600 text-white">Delete</button>
        </form>
    </div>
    @endcan

    <div class="max-w-3xl mx-auto p-4 space-y-3">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="text-sm text-gray-500">
            {{ $contribution->user->name ?? 'User' }} •
            {{ $contribution->created_at->format('M j, Y H:i') }}
            @if ($contribution->is_edited)
                <span class="ml-2 px-2 py-0.5 text-xs rounded bg-gray-100">Edited</span>
            @endif
            @if ($contribution->accepted_at)
                <span class="ml-2 px-2 py-0.5 text-xs rounded bg-green-100 text-green-800">Endorsed</span>
            @endif
        </div>

        @if ($contribution->content)
            <div class="prose max-w-none">
                {!! nl2br(e($contribution->content)) !!}
            </div>
        @endif

        @if ($contribution->file_path)
            <div class="border rounded p-3">
                <a class="underline" href="{{ route('groups.contributions.file', [$group, $contribution]) }}">
                    View / download attached file
                </a>
                <div class="text-xs text-gray-500 mt-1">{{ $contribution->mime_type }}</div>
            </div>
        @endif

        {{-- Helpful --}}
        @can('view', $contribution)
            @if (auth()->id() !== $contribution->user_id)
                <form method="POST" action="{{ route('groups.contributions.helpful.toggle', [$group, $contribution]) }}" class="mt-2 inline-block">
                    @csrf
                    <button class="px-3 py-1 rounded border">
                        {{ $hasHelpful ? 'Unmark Helpful' : 'Mark Helpful' }}
                    </button>
                </form>
            @endif
            <div class="text-sm text-gray-500 mt-1">
                Helpful: {{ $contribution->helpfuls()->count() }}
            </div>
        @endcan

        {{-- Endorse / Unendorse (owner or moderator) --}}
        @can('endorse', $contribution)
            @if (auth()->id() !== $contribution->user_id)
                @if (!$contribution->accepted_at)
                    <form method="POST" action="{{ route('contributions.endorse', [$group, $contribution]) }}" class="mt-2 inline-block">
                        @csrf
                        <button class="px-3 py-1 rounded border">Mark as Accepted</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('contributions.unendorse', [$group, $contribution]) }}" class="mt-2 inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 rounded border">Un-accept</button>
                    </form>
                @endif
            @endif
        @endcan

        @if ($contribution->accepted_at)
            <div class="text-sm text-green-700">
                Accepted {{ $contribution->accepted_at?->diffForHumans() }}
                @if ($contribution->accepted_by)
                    by {{ optional(\App\Models\User::find($contribution->accepted_by))->name ?? 'owner' }}
                @endif
            </div>
        @endif

        <hr class="my-4">

        <h3 class="font-semibold">Comments</h3>

        {{-- Add comment (members only) --}}
        @can('create', [\App\Models\ContributionComment::class, $contribution])
        <form method="POST" action="{{ route('groups.contributions.comments.store', [$group, $contribution]) }}" class="space-y-2 mb-4">
            @csrf
            <textarea name="body" rows="3" class="w-full border rounded p-2" placeholder="Write a comment...">{{ old('body') }}</textarea>
            @error('body') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            <button class="px-3 py-1 rounded bg-blue-600 text-white">Post comment</button>
        </form>
        @endcan

        {{-- List comments --}}
        <div class="space-y-3">
            @foreach ($contribution->comments()->with('user')->latest()->get() as $cm)
                <div class="p-3 border rounded">
                    <div class="text-sm text-gray-500">
                        {{ $cm->user->name ?? 'User' }} • {{ $cm->created_at->diffForHumans() }}
                    </div>
                    <div class="mt-1">{!! nl2br(e($cm->body)) !!}</div>

                    @can('delete', $cm)
                    <form method="POST" action="{{ route('groups.contributions.comments.destroy', [$group, $contribution, $cm]) }}"
                          onsubmit="return confirm('Delete this comment?');" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button class="px-2 py-1 rounded bg-red-600 text-white text-xs">Delete</button>
                    </form>
                    @endcan
                </div>
            @endforeach
            @if ($contribution->comments()->count() === 0)
                <p class="text-gray-500 text-sm">No comments yet.</p>
            @endif
        </div>

        <div>
            <a class="text-blue-600 underline" href="{{ route('groups.contributions.index', $group) }}">Back to list</a>
        </div>
    </div>
</x-app-layout>
