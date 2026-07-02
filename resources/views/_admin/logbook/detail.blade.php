@extends('_admin._layout.app')

@section('title', 'Detail Logbook')

@section('content')
    <x-admin.page-header title="Detail Logbook" subtitle="Logbook Pengguna" />

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl overflow-hidden">

            {{-- Header: tanggal besar + label entry --}}
            <div class="px-8 pt-8 pb-7">
                <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-3">
                    Entri Logbook
                </p>
                <h2 class="text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                    {{ $logbook['tanggal'] }}
                </h2>
            </div>

            <div class="mx-8 border-t border-dashed border-gray-200 dark:border-neutral-700"></div>

            {{-- Konten: deskripsi dengan aksen garis kiri --}}
            <div class="px-8 py-8">
                <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-5">
                    Deskripsi Kegiatan
                </p>
                <div class="border-l-2 border-gray-200 dark:border-neutral-700 pl-6">
                    <p class="text-[15px] leading-8 text-gray-700 dark:text-neutral-300 whitespace-pre-line">
                        {{ $logbook['deskripsi'] }}
                    </p>
                </div>
            </div>

        </div>

        <div class="mt-6 flex items-center gap-x-2">
            <x-admin.button href="{{ route('admin.logbook.index') }}" color="outline-secondary">
                Kembali
            </x-admin.button>
        </div>
    </div>
@endsection