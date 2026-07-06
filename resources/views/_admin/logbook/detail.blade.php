@extends('_admin._layout.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Detail Logbook')

@section('content')
    <x-admin.page-header title="Detail Logbook" subtitle="Logbook Pengguna" />

    <x-admin.card fit class="max-w-2xl">
        <div class="space-y-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-500 mb-1">
                    Tanggal
                </p>
                <p class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                    {{ $logbook['tanggal'] }}
                </p>
            </div>

            <div class="pt-1 border-t border-gray-100 dark:border-neutral-800">
                <p class="text-sm font-medium text-gray-500 dark:text-neutral-500 mb-2 mt-4">
                    Deskripsi Kegiatan
                </p>
                <p class="text-sm text-gray-800 dark:text-neutral-200 leading-6 whitespace-pre-line">
                    {{ $logbook['deskripsi'] }}
                </p>
            </div>

            @if($images->isNotEmpty())
                <div class="pt-1 border-t border-gray-100 dark:border-neutral-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-500 mb-3 mt-4">
                        Dokumentasi Foto ({{ $images->count() }})
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($images as $image)
                            <div class="relative group">
                                <a href="{{ Storage::url($image->file_path) }}" target="_blank" class="block">
                                    <img src="{{ Storage::url($image->file_path) }}"
                                        class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-neutral-700 group-hover:opacity-75 transition">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-admin.card>

    <div class="mt-5 flex items-center gap-x-2">
        <x-admin.button href="{{ route('admin.logbook.update', $logbook['id']) }}" color="primary" class="font-bold">
            Edit Logbook
        </x-admin.button>
        <x-admin.button href="{{ route('admin.logbook.index') }}" color="outline-secondary">
            Kembali
        </x-admin.button>
    </div>
@endsection