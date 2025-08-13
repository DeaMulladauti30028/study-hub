<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Study Groups
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
    
            {{-- This is the button --}}
            <div class="flex justify-end">
                <a href="{{ route('groups.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + New Group
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @forelse ($groups as $g)
                    <div class="py-3 border-b border-gray-200/50 last:border-none flex items-start justify-between gap-4">
                        <div>
                            <div class="font-semibold">{{ $g->name }}</div>
                            <div class="text-sm text-gray-500">
                                Course: {{ $g->course?->title }} ({{ $g->course?->code }}) • {{ $g->members_count }} member{{ $g->members_count === 1 ? '' : 's' }}
                            </div>
                            @if($g->description)
                                <p class="mt-1 text-sm">{{ $g->description }}</p>
                            @endif
                        </div>
                    
                        <div class="shrink-0">
                            @if(in_array($g->id, $myGroupIds ?? []))
                                <form method="POST" action="{{ route('groups.leave', $g) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700">
                                        Leave
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('groups.join', $g) }}">
                                    @csrf
                                    <button class="px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                                        Join
                                    </button>
                                </form>
                            @endif
                            <a class="text-sm underline" href="{{ route('groups.sessions.index', $g) }}">Sessions</a>

                        </div>
                    </div>
                    
                    @empty
                        <p>No study groups yet.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
