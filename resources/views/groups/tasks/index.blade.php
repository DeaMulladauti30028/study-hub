<x-app-layout>
    <x-slot name="header">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <h2 class="font-semibold text-xl">Study Tasks</h2>
          <span id="tasks-completed-count"
                class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
            You’ve completed {{ $completedCount }} tasks
          </span>
        </div>
  
        @can('create', [\App\Models\Assignment::class, $group])
          <a href="{{ route('groups.tasks.create', $group) }}"
             class="inline-flex items-center px-3 py-1.5 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">
            + New Task
          </a>
        @endcan
      </div>
    </x-slot>
  
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-4">
  
      {{-- Toolbar --}}
      <form method="GET" class="flex flex-wrap items-center gap-3 text-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tasks…"
               class="border rounded px-2 py-1">
        <select name="filter" class="border rounded px-2 py-1" onchange="this.form.submit()">
          @foreach (['all'=>'All','mine'=>'Mine','done'=>'Done','pending'=>'Pending'] as $k=>$v)
            <option value="{{ $k }}" @selected($filter===$k)>{{ $v }}</option>
          @endforeach
        </select>
        <select name="sort" class="border rounded px-2 py-1" onchange="this.form.submit()">
          <option value="due" @selected($sort==='due')>Due soonest</option>
          <option value="newest" @selected($sort==='newest')>Newest</option>
          <option value="oldest" @selected($sort==='oldest')>Oldest</option>
        </select>
        <button class="px-3 py-1 border rounded">Apply</button>
      </form>
  
      {{-- List --}}
      <div class="bg-white dark:bg-gray-900 shadow rounded divide-y">
        @forelse ($assignments as $a)
          @php
            $done = auth()->user()->assignments->firstWhere('id', $a->id)?->pivot?->done_at !== null;
            $now  = now(config('app.timezone'));
            $overdue = $a->due_at && $a->due_at->lt($now);
            $duesoon = $a->due_at && !$overdue && $a->due_at->lte($now->copy()->addDays(3));
          @endphp
  
          <div class="p-4 flex items-start justify-between gap-4"
               data-assignment-id="{{ $a->id }}">
            <div class="min-w-0">
              <div class="font-medium truncate">{{ $a->title }}</div>
              @if($a->description)
                <div class="text-sm text-gray-600 dark:text-gray-300 mt-0.5 line-clamp-2">{{ $a->description }}</div>
              @endif
              <div class="text-xs text-gray-500 mt-1">
                @if ($a->due_at)
                  Due: {{ $a->due_at->timezone(config('app.timezone'))->format('M d, Y H:i') }}
                @else
                  No due date
                @endif
              </div>
              <div class="mt-1 flex items-center gap-2 text-xs">
                @if ($overdue)
                  <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200">Overdue</span>
                @elseif ($duesoon)
                  <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">Due soon</span>
                @endif
                <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200 {{ $done ? '' : 'hidden' }}"
                      data-done-badge>✓ Done</span>
              </div>
            </div>
  
            <div class="shrink-0 space-x-2">
              <button
                class="js-toggle-done text-sm px-3 py-1 rounded border"
                data-url="{{ route('groups.tasks.toggle', [$group, $a]) }}"
                data-initial="{{ $done ? '1' : '0' }}">
                {{ $done ? 'Unmark' : 'Mark done' }}
              </button>
  
              @can('update', $a)
                <a href="{{ route('groups.tasks.edit', [$group, $a]) }}"
                   class="text-sm px-3 py-1 rounded border">Edit</a>
              @endcan
              @can('delete', $a)
                <form method="POST" action="{{ route('groups.tasks.destroy', [$group, $a]) }}" class="inline">
                  @csrf @method('DELETE')
                  <button class="text-sm px-3 py-1 rounded border"
                          onclick="return confirm('Delete this task?')">Delete</button>
                </form>
              @endcan
            </div>
          </div>
        @empty
          <div class="p-6 text-sm text-gray-500">No tasks found.</div>
        @endforelse
      </div>
  
      <div>{{ $assignments->links() }}</div>
    </div>
  
    {{-- AJAX toggle --}}
    <script>
    (function () {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const headerEl = document.getElementById('tasks-completed-count');
  
      function updateHeaderCount(count) {
        if (headerEl) headerEl.textContent = `You’ve completed ${count} tasks`;
      }
  
      async function toggleDone(btn) {
        const url = btn.dataset.url;
        const row = btn.closest('[data-assignment-id]');
        if (!url || !row) return;
  
        btn.disabled = true;
        try {
          const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token || '', 'Accept': 'application/json' },
          });
          if (!res.ok) throw new Error('Network error');
          const data = await res.json();
  
          btn.textContent = data.done ? 'Unmark' : 'Mark done';
          const badge = row.querySelector('[data-done-badge]');
          if (badge) badge.classList.toggle('hidden', !data.done);
          if (typeof data.completedCount === 'number') updateHeaderCount(data.completedCount);
        } catch (e) {
          console.error(e);
          alert('Could not update. Please try again.');
        } finally {
          btn.disabled = false;
        }
      }
  
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-toggle-done');
        if (!btn) return;
        e.preventDefault();
        toggleDone(btn);
      });
    })();
    </script>
  </x-app-layout>
  