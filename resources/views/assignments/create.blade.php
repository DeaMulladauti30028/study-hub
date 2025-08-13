<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            New Assignment — {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('groups.assignments.store', $group) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm mb-1">Title</label>
                            <input name="title" value="{{ old('title') }}" class="w-full rounded border-gray-300" required>
                            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Due at (optional)</label>
                            <input type="datetime-local" name="due_at" class="w-full rounded border-gray-300">
                            @error('due_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Description (optional)</label>
                            <textarea name="description" rows="4" class="w-full rounded border-gray-300">{{ old('description') }}</textarea>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('groups.assignments.index', $group) }}" class="px-4 py-2 rounded border">Cancel</a>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create assignment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
