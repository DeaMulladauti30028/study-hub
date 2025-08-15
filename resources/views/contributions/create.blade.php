<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">New Contribution</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4 space-y-4">
        <form method="POST" action="{{ route('groups.contributions.store', $group) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Title</label>
                <input name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
                @error('title') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Content (optional)</label>
                <textarea name="content" rows="5" class="w-full border rounded p-2">{{ old('content') }}</textarea>
                @error('content') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">File (optional)</label>
                <input type="file" name="file" class="w-full">
                @error('file') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <p class="text-sm text-gray-500">Provide text or upload a file (at least one is required).</p>

            <div class="flex gap-2">
                <a href="{{ route('groups.contributions.index', $group) }}" class="px-3 py-2 rounded border">Cancel</a>
                <button class="px-3 py-2 rounded bg-blue-600 text-white">Post</button>
            </div>
        </form>
    </div>
</x-app-layout>
