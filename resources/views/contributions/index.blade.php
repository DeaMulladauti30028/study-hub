<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $group->name }} — Contributions</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto p-4 space-y-4">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <form method="GET" class="flex flex-wrap items-center gap-3 mb-3">
            <div>
                <label class="text-sm mr-1">Filter</label>
                <select name="filter" class="border rounded p-1" onchange="this.form.submit()">
                    <option value="" @selected(request('filter')===null)>All</option>
                    <option value="accepted" @selected(request('filter')==='accepted')>Accepted</option>
                    <option value="mine" @selected(request('filter')==='mine')>My posts</option>
                </select>
            </div>
        
            <div>
                <label class="text-sm mr-1">Sort</label>
                <select name="sort" class="border rounded p-1" onchange="this.form.submit()">
                    <option value="newest" @selected(request('sort')==='newest' || request('sort')===null)>Newest</option>
                    <option value="helpful" @selected(request('sort')==='helpful')>Most helpful</option>
                    <option value="oldest" @selected(request('sort')==='oldest')>Oldest</option>
                </select>
            </div>
        
            @if (request()->has('filter') || request()->has('sort'))
                <a href="{{ route('groups.contributions.index', $group) }}" class="text-sm underline">Reset</a>
            @endif
        </form>
        

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
                
                <div class="text-xs text-gray-500">
                    {{ $c->user->name ?? 'User' }} • {{ $c->created_at->diffForHumans() }}
                    • Helpful: {{ $c->helpfuls_count }}
                </div>
                @if ($c->is_accepted)
                    <span class="ml-2 px-2 py-0.5 text-xs rounded bg-green-100 text-green-800">Accepted</span>
                @endif
            @empty
                <p class="text-gray-500">No contributions yet.</p>
            @endforelse
        </div>

        {{ $items->links() }}
    </div>
</x-app-layout>
