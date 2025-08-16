<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $group->name }}</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-6">
        {{-- Group summary --}}
        <div class="p-4 rounded border">
            <div class="font-medium">Course: {{ $group->course->title ?? '—' }}</div>
            @if($group->nextSession)
                <div class="text-sm mt-1">Next session: {{ $group->nextSession->starts_at }}</div>
            @endif
        </div>

        {{-- Members + moderation --}}
        <div class="p-4 rounded border">
            <h3 class="text-lg font-semibold mb-3">
                Members ({{ $group->members->count() }})
            </h3>

            @foreach($group->members as $m)
                <div class="flex items-center justify-between p-2 border rounded mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ $m->name }}</span>

                        @if((int)$group->owner_id === (int)$m->id)
                            <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-800">Owner</span>
                        @elseif((bool)($m->pivot->is_moderator ?? false))
                            <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800">Moderator</span>
                        @endif
                    </div>

                    @can('manageModerators', $group)
                        @if((int)$group->owner_id !== (int)$m->id)
                            <div class="flex items-center gap-2">
                                @if(!($m->pivot->is_moderator ?? false))
                                    <form method="POST" action="{{ route('groups.moderators.promote', [$group, $m]) }}">
                                        @csrf
                                        <button class="px-3 py-1 text-sm rounded bg-blue-600 text-white">Promote</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('groups.moderators.demote', [$group, $m]) }}">
                                        @csrf
                                        <button class="px-3 py-1 text-sm rounded bg-gray-700 text-white">Demote</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @endcan
                </div>
            @endforeach

            @if (session('status'))
                <div class="mt-3 p-2 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
