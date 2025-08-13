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
    
            <div class="flex items-center justify-between">
                <form method="GET" action="{{ route('groups.index') }}" class="flex items-center gap-4 text-sm">
                    <label class="inline-flex items-center gap-2">
                        <input id="upcoming" type="checkbox" name="upcoming" value="1"
                               @checked(request('upcoming')) onchange="this.form.submit()">
                        <span>Only groups with upcoming sessions</span>
                    </label>
            
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search groups or courses…"
                            class="rounded border-gray-300"
                        >
                        <button class="px-3 py-1.5 rounded border">Search</button>
                    </div>
                </form>
            
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

                        @if($g->nextSession)
                            <div class="mt-1 inline-flex items-center gap-2 text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200">
                                Next: {{ $g->nextSession->starts_at->format('Y-m-d H:i') }}
                                <span class="text-gray-500">• {{ $g->nextSession->duration_minutes }} min</span>
                                <span class="text-gray-500">• {{ $g->nextSession->starts_at->diffForHumans() }}</span>
                            </div>
                        @else
                            <div class="mt-1 text-xs text-gray-500">No upcoming sessions</div>
                        @endif
                    
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
