<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Courses
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('courses.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + New Course
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @forelse ($courses as $course)
                        <div class="py-3 border-b border-gray-200/50 last:border-none">
                            <div class="font-semibold">{{ $course->title }}</div>
                            <div class="text-sm text-gray-500">{{ $course->code }}</div>
                            @if($course->description)
                                <p class="mt-1 text-sm">{{ $course->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p>No courses yet.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
