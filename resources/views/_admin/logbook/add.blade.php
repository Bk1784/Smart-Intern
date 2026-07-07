@extends('_admin._layout.app')

@section('title', 'Tambah Logbook')

@section('content')

    <x-admin.page-header title="Edit Logbook" subtitle="Logbook Pengguna" :back-url="route('admin.logbook.index')" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.logbook.create') }}" method="POST" navigate-form enctype="multipart/form-data">

            <x-admin.card fit>
                @csrf

                <div class="space-y-5">
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

                        <div>
                        <x-admin.file-dropzone
                            name="images[]"
                            label="Foto Kegiatan"
                            hint="Bisa pilih lebih dari 1 foto. Maksimal 10 foto, masing-masing 5MB." />
                        </div>
                    </div>
                </div>
            </x-admin.card>

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
    @endpush
@endsection