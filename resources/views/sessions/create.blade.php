<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            New Session — {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('groups.sessions.store', $group) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm mb-1">Starts at</label>
                            <input type="datetime-local" name="starts_at" class="w-full rounded border-gray-300" required>
                            @error('starts_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" min="15" max="480" value="60" class="w-full rounded border-gray-300" required>
                            @error('duration_minutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Video URL (optional)</label>
                            <input type="url" name="video_url" class="w-full rounded border-gray-300" placeholder="https://meet.google.com/…">
                            @error('video_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Notes (optional)</label>
                            <textarea name="notes" rows="4" class="w-full rounded border-gray-300"></textarea>
                            @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('groups.sessions.index', $group) }}" class="px-4 py-2 rounded border">Cancel</a>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create session</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
