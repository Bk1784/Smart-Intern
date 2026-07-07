@extends('_admin._layout.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Logbook')

@section('content')

    <x-admin.page-header title="Edit Logbook" subtitle="Logbook Pengguna" :back-url="route('admin.logbook.index')" />

    <div class="max-w-2xl space-y-5">

        {{-- Foto Tersimpan --}}
        <x-admin.card fit>
            <p class="text-sm font-medium text-gray-500 dark:text-neutral-500 mb-3">
                Foto Tersimpan ({{ $images->count() }})
            </p>

            @if($images->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach($images as $image)
                        <div class="relative group">
                            <a href="{{ Storage::url($image->file_path) }}" target="_blank" class="block">
                                <img src="{{ Storage::url($image->file_path) }}"
                                    class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-neutral-700 group-hover:opacity-75 transition">
                            </a>
                            <button type="button"
                                class="delete-photo-trigger absolute top-1 right-1 size-6 inline-flex items-center justify-center rounded-full bg-white/90 text-red-600 hover:bg-white shadow-sm dark:bg-neutral-900/90"
                                title="Hapus foto"
                                data-hs-overlay="#delete-photo-modal"
                                data-action="{{ route('admin.logbook.image_delete', $image->id) }}">
                                @include('_admin._layout.icons.trash')
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-neutral-500">Belum ada foto tersimpan.</p>
            @endif
        </x-admin.card>

        {{-- Form Update --}}
        <form action="{{ route('admin.logbook.do_update', $logbook->id) }}" method="POST" navigate-form enctype="multipart/form-data">

            <x-admin.card fit>
                @csrf

                <div class="space-y-5">
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

                    <div>
                        <x-admin.file-dropzone
                            name="images[]"
                            label="Tambah Foto Kegiatan"
                            hint="Foto baru akan ditambahkan ke foto yang sudah ada. Maksimal 10 foto sekaligus, masing-masing 5MB." />
                    </div>
                </div>
            </x-admin.card>

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

    {{-- Delete Photo Confirmation Modal --}}
    <div id="delete-photo-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto"
        role="dialog" tabindex="-1" aria-labelledby="delete-photo-modal-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
            <div
                class="relative flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                <div class="absolute top-2 end-2">
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600"
                        aria-label="Close" data-hs-overlay="#delete-photo-modal">
                        <span class="sr-only">Close</span>
                        @include('_admin._layout.icons.close_modal')
                    </button>
                </div>

                <div class="p-4 sm:p-10 text-center overflow-y-auto">
                    <span
                        class="mb-4 inline-flex justify-center items-center size-14 rounded-full border-4 border-red-50 bg-red-100 text-red-500 dark:bg-red-700 dark:border-red-600 dark:text-red-100">
                        @include('_admin._layout.icons.warning_modal')
                    </span>

                    <h3 id="delete-photo-modal-label" class="mb-2 text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Hapus Foto
                    </h3>
                    <p class="text-gray-500 dark:text-neutral-500">
                        Apakah Anda yakin ingin menghapus foto ini?
                        <br>Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex justify-center gap-x-4">
                        <button type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            data-hs-overlay="#delete-photo-modal">
                            Batal
                        </button>
                        <form id="delete-photo-form" method="POST" class="inline" navigate-form>
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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

            document.querySelectorAll('.delete-photo-trigger').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('delete-photo-form').action = this.dataset.action;
                });
            });
        </script>
    @endpush
@endsection