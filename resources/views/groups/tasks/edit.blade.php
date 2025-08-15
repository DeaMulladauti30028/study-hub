<x-app-layout>
    <x-slot name="header">
      <h2 class="font-semibold text-xl">Edit Study Task</h2>
    </x-slot>
  
    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
      <form method="POST" action="{{ route('groups.tasks.update', [$group, $assignment]) }}" class="space-y-4">
        @csrf @method('PUT')
  
        <div>
          <label class="block text-sm font-medium">Title</label>
          <input name="title" value="{{ old('title', $assignment->title) }}" class="w-full border rounded px-2 py-1" required>
          @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
  
        <div>
          <label class="block text-sm font-medium">Description</label>
          <textarea name="description" rows="4" class="w-full border rounded px-2 py-1">{{ old('description', $assignment->description) }}</textarea>
        </div>
  
        <div>
          <label class="block text-sm font-medium">Due at</label>
          <input type="datetime-local" name="due_at"
                 value="{{ old('due_at', optional($assignment->due_at)->format('Y-m-d\TH:i')) }}"
                 class="border rounded px-2 py-1">
        </div>
  
        <div class="flex justify-end gap-2">
          <a href="{{ route('groups.tasks.index', $group) }}" class="px-3 py-1.5 border rounded">Cancel</a>
          <button class="px-3 py-1.5 bg-indigo-600 text-white rounded">Save</button>
        </div>
      </form>
    </div>
  </x-app-layout>
  