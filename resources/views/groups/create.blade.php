<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            New Study Group
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('groups.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm mb-1">Course</label>
                            <select name="course_id" class="w-full rounded border-gray-300" required>
                                <option value="" disabled selected>— Select a course —</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                            @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Group name</label>
                            <input name="name" value="{{ old('name') }}" class="w-full rounded border-gray-300" required>
                            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Description (optional)</label>
                            <textarea name="description" rows="4" class="w-full rounded border-gray-300">{{ old('description') }}</textarea>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('groups.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
