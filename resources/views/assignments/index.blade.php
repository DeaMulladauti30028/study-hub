<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Assignments — {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('groups.index') }}" class="text-sm underline">← Back to groups</a>

                @if($isMember)
                    <a href="{{ route('groups.assignments.create', $group) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        + New Assignment
                    </a>
                @else
                    <form method="POST" action="{{ route('groups.join', $group) }}">
                        @csrf
                        <button class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
                            Join group to add assignments
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    @forelse ($assignments as $a)
                        <div class="py-3 border-b border-gray-200/50 last:border-none">
                            <div class="font-semibold">{{ $a->title }}</div>

                            @if($a->due_at)
                                @php
                                    $overdue = now()->greaterThan($a->due_at);
                                @endphp
                                <div class="text-sm {{ $overdue ? 'text-red-600' : 'text-gray-500' }}">
                                    Due: {{ $a->due_at->format('Y-m-d H:i') }}
                                    <span class="ml-1">• {{ $a->due_at->diffForHumans() }}</span>
                                    @if($overdue)
                                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">Overdue</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">No due date</div>
                            @endif

                            @if($a->description)
                                <p class="mt-1 text-sm">{{ $a->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p>No assignments yet.</p>
                    @endforelse

                    <div>{{ $assignments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
