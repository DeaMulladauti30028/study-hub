<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $group->name }} — Contributions</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto p-4 space-y-4">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex justify-end">
            <a class="px-3 py-2 rounded bg-blue-600 text-white" href="{{ route('groups.contributions.create', $group) }}">
                New contribution
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($items as $c)
                <a href="{{ route('groups.contributions.show', [$group, $c]) }}" class="block p-3 rounded border hover:bg-gray-50">
                    <div class="text-sm text-gray-500">
                        {{ $c->user->name ?? 'User' }} • {{ $c->created_at->diffForHumans() }}
                    </div>
                    <div class="font-medium">{{ $c->title }}</div>
                </a>
            @empty
                <p class="text-gray-500">No contributions yet.</p>
            @endforelse
        </div>

        {{ $items->links() }}
    </div>
</x-app-layout>
