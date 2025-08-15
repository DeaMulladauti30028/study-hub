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
        <div class="text-sm text-gray-500">
            {{ $contribution->user->name ?? 'User' }} •
            {{ $contribution->created_at->format('M j, Y H:i') }}
            @if ($contribution->is_edited)
                <span class="ml-2 px-2 py-0.5 text-xs rounded bg-gray-100">Edited</span>
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

        @if ($contribution->is_accepted)
            <div class="text-sm text-green-700">
                Accepted {{ $contribution->accepted_at?->diffForHumans() }}
                @if ($contribution->accepted_by)
                    by {{ optional(\App\Models\User::find($contribution->accepted_by))->name ?? 'owner' }}
                @endif
            </div>
        @endif
  
        @can('endorse', $contribution)
            @if (auth()->id() !== $contribution->user_id)
                <form method="POST" action="{{ route('groups.contributions.endorse.toggle', [$group, $contribution]) }}" class="mt-2 inline-block">
                    @csrf
                    <button class="px-3 py-1 rounded border">
                        {{ $contribution->is_accepted ? 'Un-accept' : 'Mark as Accepted' }}
                    </button>
                </form>
            @endif
        @endcan

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
        @forelse ($contribution->comments()->with('user')->get() as $cm)
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
        @empty
            <p class="text-gray-500 text-sm">No comments yet.</p>
        @endforelse
        </div>


        <div>
            <a class="text-blue-600 underline" href="{{ route('groups.contributions.index', $group) }}">Back to list</a>
        </div>
    </div>
</x-app-layout>
