@extends('_admin._layout.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Logbook')

@section('content')

    <x-admin.page-header title="Edit Logbook" subtitle="Logbook Pengguna" />

    <div class="max-w-2xl space-y-5">

        {{-- ============================================================
             KARTU 1 — Foto Tersimpan (di luar form update, punya form
             hapus sendiri per-foto, supaya TIDAK nested form)
             ============================================================ --}}
        <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-6">
                <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-4 flex items-center gap-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                    </svg>
                    Foto Tersimpan
                    @if($images->isNotEmpty())
                        <span class="inline-flex items-center justify-center size-5 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400">
                            {{ $images->count() }}
                        </span>
                    @endif
                </p>

                @if($images->isNotEmpty())
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach($images as $image)
                            <div class="group relative aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-100 dark:bg-neutral-800">
                                <a href="{{ Storage::url($image->file_path) }}" target="_blank" class="absolute inset-0 block">
                                    <img src="{{ Storage::url($image->file_path) }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover transition duration-300 ease-out group-hover:scale-105 group-hover:brightness-90">
                                </a>

                                {{-- form hapus foto — berdiri sendiri, TIDAK di dalam form update --}}
                                <form action="{{ route('admin.logbook.image_delete', $image->id) }}" method="POST" navigate-form
                                    class="absolute top-1.5 right-1.5 z-10">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="size-6 inline-flex items-center justify-center rounded-full bg-white/95 text-red-600 hover:bg-red-50 shadow-sm opacity-0 group-hover:opacity-100 transition duration-200 dark:bg-neutral-900/95 dark:hover:bg-red-900/30"
                                        title="Hapus foto"
                                        onclick="return confirm('Hapus foto ini?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center text-center gap-y-2 py-4">
                        <div class="size-9 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center text-gray-400 dark:text-neutral-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-neutral-500">
                            Belum ada foto tersimpan
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.logbook.do_update', $logbook->id) }}" method="POST" navigate-form enctype="multipart/form-data">
            @csrf

            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header aksen --}}
                <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-blue-50/70 to-transparent dark:from-blue-900/10">
                    <div class="absolute top-0 left-0 h-full w-1 bg-blue-600"></div>
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                        Edit Entri
                    </p>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Perbarui Logbook
                    </h2>
                </div>

                <div class="px-6 py-6 border-t border-gray-100 dark:border-neutral-800 space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                            Tanggal
                        </label>
                        <x-admin.input type="text" name="tanggal" id="tanggal-input" autocomplete="off"
                            :value="old('tanggal', $logbook->tanggal)"
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
                            placeholder="Tuliskan apa yang Anda kerjakan hari ini...">{{ old('deskripsi', $logbook->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Upload foto baru --}}
                <div class="px-6 py-6 border-t border-gray-100 dark:border-neutral-800">
                    <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                        Tambah Foto Kegiatan
                    </label>
                    <label for="images-input"
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
                        Foto baru akan ditambahkan ke foto yang sudah ada. Maksimal 10 foto sekaligus, masing-masing 5MB.
                    </p>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-5 flex items-center gap-x-2">
                <x-admin.button type="submit" color="primary" class="font-bold">
                    Update
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

            const imagesInput = document.getElementById('images-input');
            const fileCountLabel = document.getElementById('file-count-label');

            imagesInput.addEventListener('change', function () {
                const count = this.files.length;
                fileCountLabel.textContent = count > 0
                    ? count + ' foto dipilih'
                    : 'Belum ada foto dipilih';
            });
        </script>
    @endpush
@endsection