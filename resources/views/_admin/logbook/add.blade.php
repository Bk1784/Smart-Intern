@extends('_admin._layout.app')

@section('title', 'Tambah Logbook')

@section('content')

    <x-admin.page-header title="Tambah Logbook" subtitle="Logbook Pengguna" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.logbook.create') }}" method="POST" navigate-form>
            @csrf

            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-800 dark:text-neutral-200">
                        Tanggal
                    </label>
                    <x-admin.input type="date" name="tanggal" :value="old('tanggal', now()->format('Y-m-d'))" />
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
            <br>

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
@endsection