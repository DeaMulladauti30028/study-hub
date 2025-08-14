
@php
function mat_category($mime) {
    $m = $mime ?? '';
    if (str_starts_with($m, 'image/')) return 'IMAGE';
    if (str_starts_with($m, 'video/')) return 'VIDEO';
    if ($m === 'application/pdf') return 'PDF';
    if (str_contains($m, 'presentation')) return 'SLIDES';
    if (str_contains($m, 'spreadsheet')) return 'SHEETS';
    if (str_contains($m, 'msword') || str_contains($m, 'word') || str_contains($m, 'text')) return 'DOCS';
    if (str_contains($m, 'zip') || str_contains($m, 'rar') || str_contains($m, '7z')) return 'ARCHIVE';
    return 'FILE';
}
@endphp

{{-- Alpine helper (hide until ready) --}}
<style>[x-cloak]{ display:none !important; }</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $group->name }} — Materials
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            {{-- Upload (members only) --}}
            @can('create', [App\Models\GroupMaterial::class, $group])
                <div class="border rounded p-4">
                    <div class="font-medium mb-2">Share a resource</div>
                    <form method="POST" action="{{ route('groups.materials.store', $group) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="file" required class="block">
                        @error('file') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                        <input type="text" name="title" class="border rounded px-3 py-2 w-full" placeholder="Title (optional)">
                        <button class="px-3 py-2 rounded bg-black text-white">Upload</button>
                    </form>
                </div>
            @endcan

            {{-- Toolbar: search + basic type filter --}}
            <form method="GET" action="{{ route('groups.materials.index', $group) }}"
                  class="flex flex-wrap items-center gap-3">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search by title…"
                       class="w-64 rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700" />

                <select name="type" class="rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700">
                    <option value="">All types</option>
                    <option value="image" @selected(($type ?? '')==='image')>Images</option>
                    <option value="pdf"   @selected(($type ?? '')==='pdf')>PDF</option>
                    <option value="video" @selected(($type ?? '')==='video')>Videos</option>
                    <option value="slides"@selected(($type ?? '')==='slides')>Slides</option>
                    <option value="sheets"@selected(($type ?? '')==='sheets')>Sheets</option>
                    <option value="docs"  @selected(($type ?? '')==='docs')>Docs</option>
                </select>

                <button class="px-3 py-2 rounded bg-black text-white">Filter</button>

                @if(($q ?? false) || ($type ?? false))
                    <a href="{{ route('groups.materials.index', $group) }}"
                       class="px-3 py-2 rounded border border-gray-300 dark:border-gray-700">Reset</a>
                @endif
            </form>

            {{-- List + Lightbox state --}}
            <div x-data="{ open:false, src:'' }" class="divide-y rounded border">
                @forelse ($materials as $m)
                    @php
                        // Using the protected preview route (private disk)
                        $previewUrl = route('groups.materials.preview', [$group, $m]);
                        $mime = $m->mime_type ?? '';
                        $previewableImage = str_starts_with($mime, 'image/');
                        $previewablePdf   = $mime === 'application/pdf';
                    @endphp

                    <div class="p-4 space-y-3">
                        {{-- Header: badge + title + meta --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 text-xs rounded bg-gray-100 dark:bg-gray-800 mr-2">
                                    {{ mat_category($m->mime_type) }}
                                </span>
                                <span class="font-medium">{{ $m->title }}</span>

                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $m->original_name ?? basename($m->file_path) }} ·
                                    @php
                                        $size = (float)$m->file_size;
                                        $human = $size > 1048576 ? round($size/1048576,1).' MB' : round($size/1024,1).' KB';
                                    @endphp
                                    {{ $human }}
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3 items-center shrink-0">
                                @can('delete', $m)
                                    <form method="POST" action="{{ route('groups.materials.destroy', [$group, $m]) }}"
                                          onsubmit="return confirm('Delete this material?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 underline">Delete</button>
                                    </form>
                                @endcan
                                <a class="underline" href="{{ route('groups.materials.download', [$group, $m]) }}">Download</a>
                            </div>
                        </div>

                        {{-- Inline preview block --}}
                        @if ($previewableImage)
                            {{-- Image preview with Alpine lightbox --}}
                            <a href="#"
                               @click.prevent="src='{{ $previewUrl }}'; open=true"
                               class="group block relative">
                                <img src="{{ $previewUrl }}" alt="{{ $m->title }}"
                                     class="w-full h-72 object-cover rounded border transition-transform group-hover:scale-105"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition"></div>
                            </a>

                        @elseif ($previewablePdf)
                            {{-- PDF preview --}}
                            <iframe src="{{ $previewUrl }}#toolbar=0&navpanes=0&scrollbar=0"
                                    class="w-full h-72 rounded border" loading="lazy"></iframe>

                        @else
                            {{-- Fallback for non-previewable files --}}
                            <div class="w-full h-72 rounded border flex flex-col items-center justify-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21h10a2 2 0 002-2V7a2 2 0 00-.586-1.414l-5-5A2 2 0 0012 0H7a2 2 0 00-2 2v17a2 2 0 002 2z" />
                                </svg>
                                <a href="{{ route('groups.materials.download', [$group, $m]) }}"
                                   class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                    Download to view
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-600 dark:text-gray-300">No materials yet.</div>
                @endforelse

                {{-- Lightbox Modal --}}
                <div x-cloak x-show="open" x-transition
                     @keydown.window.escape="open=false"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/70" @click="open=false"></div>
                    <div class="relative z-10 max-w-6xl max-h-[90vh] w-auto">
                        <button @click="open=false"
                                class="absolute -top-3 -right-3 bg-white text-black rounded-full w-8 h-8 grid place-items-center shadow">✕</button>
                        <img :src="src" alt="Preview" class="max-h-[90vh] max-w-[90vw] rounded shadow-lg">
                    </div>
                </div>
            </div>

            <div>
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
