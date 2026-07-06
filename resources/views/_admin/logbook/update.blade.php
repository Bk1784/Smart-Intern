@extends('_admin._layout.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Logbook')

@section('content')

    <x-admin.page-header title="Edit Logbook" subtitle="Logbook Pengguna" />

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
                            <form action="{{ route('admin.logbook.image_delete', $image->id) }}" method="POST" navigate-form
                                class="absolute top-1 right-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="size-6 inline-flex items-center justify-center rounded-full bg-white/90 text-red-600 hover:bg-white shadow-sm dark:bg-neutral-900/90"
                                    title="Hapus foto"
                                    onclick="return confirm('Hapus foto ini?')">
                                    @include('_admin._layout.icons.trash')
                                </button>
                            </form>
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
                        <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                            Tambah Foto Kegiatan
                        </label>
                        <input type="file" name="images[]" multiple accept="image/*"
                            class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400">
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-neutral-500">
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
    @endpush
@endsection