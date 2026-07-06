@props([
    'name' => 'images[]',
    'label' => null,
    'hint' => null,
    'multiple' => true,
    'maxFiles' => 10,
])

@php
    $fieldName = rtrim($name, '[]');
@endphp

<div class="space-y-2">
    @if ($label)
        <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
            {{ $label }}
        </label>
    @endif

    <label id="dropzone" for="images-input"
        class="dropzone-area flex flex-col items-center justify-center gap-y-2 py-6 px-4 border-2 border-dashed border-gray-200 dark:border-neutral-700 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
        </svg>
        <span class="text-sm text-gray-600 dark:text-neutral-400">
            <span class="font-semibold text-blue-600 dark:text-blue-500">Klik untuk pilih foto</span> atau seret ke sini
        </span>
        <span id="file-count-label" class="text-xs text-gray-400 dark:text-neutral-500">
            Belum ada foto dipilih
        </span>
    </label>

    <input type="file" id="images-input" name="{{ $name }}"
        {{ $multiple ? 'multiple' : '' }} accept="image/*"
        class="hidden" data-max-files="{{ $maxFiles }}">

    @if ($hint)
        <p class="mt-1.5 text-xs text-gray-400 dark:text-neutral-500">
            {{ $hint }}
        </p>
    @endif

    @error($fieldName)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error($fieldName . '.*')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div id="preview-wrapper" class="hidden mt-3">
        <div id="preview-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-3"></div>
    </div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('js/logbook-image-upload.js') }}"></script>
    @endpush
@endonce