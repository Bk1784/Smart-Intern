@extends('_admin._layout.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Detail Logbook')

@section('content')
    <x-admin.page-header title="Detail Logbook" subtitle="Logbook Pengguna" />

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl overflow-hidden shadow-sm">

            {{-- Header: aksen warna + tanggal besar --}}
            <div class="relative px-8 pt-8 pb-7 bg-gradient-to-br from-blue-50/70 to-transparent dark:from-blue-900/10">
                <div class="absolute top-0 left-0 h-full w-1 bg-blue-600"></div>
                <p class="text-xs font-semibold text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-2">
                    Entri Logbook
                </p>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ $logbook['tanggal'] }}
                </h2>
            </div>

            {{-- Konten: deskripsi --}}
            <div class="px-8 py-8 border-t border-gray-100 dark:border-neutral-800">
                <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-4 flex items-center gap-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    Deskripsi Kegiatan
                </p>
                <p class="text-[15px] leading-8 text-gray-700 dark:text-neutral-300 whitespace-pre-line">
                    {{ $logbook['deskripsi'] }}
                </p>
            </div>

            {{-- Dokumentasi foto --}}
            @if($images->isNotEmpty())
                <div class="px-8 py-8 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-800/20">
                    <p class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-5 flex items-center gap-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Dokumentasi Foto
                        <span class="inline-flex items-center justify-center size-5 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400">
                            {{ $images->count() }}
                        </span>
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($images as $image)
                            <div class="group relative aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-100 dark:bg-neutral-800">
                                <a href="{{ Storage::url($image->file_path) }}"
                                   target="_blank"
                                   class="absolute inset-0 block">
                                    <img src="{{ Storage::url($image->file_path) }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover transition duration-300 ease-out group-hover:scale-105 group-hover:brightness-90">
                                </a>

                                {{-- overlay zoom icon saat hover --}}
                                <a href="{{ Storage::url($image->file_path) }}" target="_blank"
                                   class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                    <span class="size-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                    </span>
                                </a>

                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-8 py-10 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-800/20">
                    <div class="flex flex-col items-center text-center gap-y-2">
                        <div class="size-10 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center text-gray-400 dark:text-neutral-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 dark:text-neutral-500">
                            Belum ada dokumentasi foto untuk entri ini
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 flex items-center gap-x-2">
            <x-admin.button href="{{ route('admin.logbook.update', $logbook['id']) }}" color="primary" class="font-bold">
                Edit Logbook
            </x-admin.button>
            <x-admin.button href="{{ route('admin.logbook.index') }}" color="outline-secondary">
                Kembali
            </x-admin.button>
        </div>
    </div>
@endsection