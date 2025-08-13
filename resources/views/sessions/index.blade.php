<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Sessions — {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('groups.index') }}" class="text-sm underline">← Back to groups</a>
                <a href="{{ route('groups.sessions.create', $group) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + New Session
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    @forelse ($sessions as $s)
                        <div class="py-3 border-b border-gray-200/50 last:border-none">
                            <div class="font-semibold">
                                {{ $s->starts_at?->format('Y-m-d H:i') }}
                                <span class="text-sm text-gray-500">• {{ $s->duration_minutes }} min</span>
                            </div>
                            @if($s->video_url)
                                <div class="text-sm">
                                    Video: <a class="underline" href="{{ $s->video_url }}" target="_blank" rel="noopener">join link</a>
                                </div>
                            @endif
                            @if($s->notes)
                                <p class="mt-1 text-sm">{{ $s->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p>No sessions yet.</p>
                    @endforelse

                    <div>
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
