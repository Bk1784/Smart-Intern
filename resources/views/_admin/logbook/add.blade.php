@extends('_admin._layout.app')

@section('title', 'Tambah Logbook')

@section('content')

    <x-admin.page-header title="Tambah Logbook" subtitle="Logbook Pengguna" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.logbook.create') }}" method="POST" navigate-form enctype="multipart/form-data">
            @csrf

            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header aksen, konsisten dengan halaman detail & edit --}}
                <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-blue-50/70 to-transparent dark:from-blue-900/10">
                    <div class="absolute top-0 left-0 h-full w-1 bg-blue-600"></div>
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                        Entri Baru
                    </p>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Tambah Logbook
                    </h2>
                </div>

                <div class="px-6 py-6 border-t border-gray-100 dark:border-neutral-800 space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                            Tanggal
                        </label>
                        <x-admin.input type="text" name="tanggal" id="tanggal-input" autocomplete="off"
                            :value="old('tanggal', now()->format('Y-m-d'))"
                            placeholder="Pilih tanggal..." />
                        @error('tanggal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                            Deskripsi Kegiatan
                        </label>
                        <textarea name="deskripsi" rows="5"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Tuliskan apa yang Anda kerjakan hari ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Upload foto --}}
                <div class="px-6 py-6 border-t border-gray-100 dark:border-neutral-800">
                    <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                        Foto Kegiatan
                    </label>
                    <label for="images-input" id="dropzone"
                        class="flex flex-col items-center justify-center gap-y-2 py-6 px-4 border-2 border-dashed border-gray-200 dark:border-neutral-700 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-neutral-400">
                            <span class="font-semibold text-blue-600 dark:text-blue-500">Klik untuk pilih foto</span> atau seret ke sini
                        </span>
                        <span class="text-xs text-gray-400 dark:text-neutral-500" id="file-count-label">
                            Belum ada foto dipilih
                        </span>
                    </label>
                    <input id="images-input" type="file" name="images[]" multiple accept="image/*" class="hidden">
                    <p class="mt-2 text-xs text-gray-400 dark:text-neutral-500">
                        Bisa pilih lebih dari 1 foto. Maksimal 10 foto, masing-masing 5MB.
                    </p>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Preview foto yang dipilih, sebelum submit --}}
                    <div id="preview-wrapper" class="hidden mt-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-3">
                            Foto Akan Diupload
                        </p>
                        <div id="preview-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-3"></div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center gap-x-2">
                <x-admin.button type="submit" color="primary" class="font-bold">
                    Simpan
                </x-admin.button>
                <x-admin.button href="{{ route('admin.logbook.index') }}" color="outline-secondary">
                    Batal
                </x-admin.button>
            </div>
        </form>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

        <script>
            flatpickr("#tanggal-input", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                maxDate: "today",
            });
        </script>

        <script src="{{ asset('js/logbook-image-upload.js') }}"></script>
    @endpush
@endsection