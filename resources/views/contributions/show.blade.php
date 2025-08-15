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

        <div>
            <a class="text-blue-600 underline" href="{{ route('groups.contributions.index', $group) }}">Back to list</a>
        </div>
    </div>
</x-app-layout>
