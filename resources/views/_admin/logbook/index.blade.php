@extends('_admin._layout.app')

@section('title', 'Logbook Pengguna')

@section('content')
    <x-admin.page-header title="Data Logbook" subtitle="Logbook Pengguna">
        <div class="flex items-center gap-x-2">
           <div class="hs-dropdown relative inline-flex">
            <button type="button" 
                class="hs-dropdown-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-bold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                @include('_admin._layout.icons.download')
                Download
            </button>

            <div class="hs-dropdown-menu w-72 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden z-20 bg-white rounded-xl shadow-lg dark:bg-neutral-800 dark:border dark:border-neutral-700 p-4 mt-2"
                onclick="event.stopPropagation()">

                {{-- Mode: Rentang Custom --}}
                <form action="{{ route('admin.logbook.download') }}" method="GET" class="mb-4 pb-4 border-b border-gray-100 dark:border-neutral-700">
                    <input type="hidden" name="type" value="custom">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Rentang Tanggal</p>
                    <div class="flex items-center gap-2 mb-2">
                        <x-admin.input type="date" name="start_date" size="sm" />
                        <x-admin.input type="date" name="end_date" size="sm" />
                    </div>
                    <x-admin.button type="submit" size="sm" color="primary" class="w-full justify-center">
                        Download
                    </x-admin.button>
                </form>

                {{-- Mode: Mingguan --}}
                <form action="{{ route('admin.logbook.download') }}" method="GET" class="mb-4 pb-4 border-b border-gray-100 dark:border-neutral-700">
                    <input type="hidden" name="type" value="weekly">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Per Minggu (Senin-Jumat)</p>
                    <input type="week" name="week" required
                        class="py-2 px-3 mb-2 block w-full border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300">
                    <x-admin.button type="submit" size="sm" color="primary" class="w-full justify-center">
                        Download
                    </x-admin.button>
                </form>

                {{-- Mode: Bulanan --}}
                <form action="{{ route('admin.logbook.download') }}" method="GET">
                    <input type="hidden" name="type" value="monthly">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Per Bulan</p>
                    <input type="month" name="month" required
                        class="py-2 px-3 mb-2 block w-full border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300">
                    <x-admin.button type="submit" size="sm" color="primary" class="w-full justify-center">
                        Download
                    </x-admin.button>
                </form>

            </div>
            </div>
            
            <x-admin.button href="{{ route('admin.logbook.add') }}" class="font-bold">
                @include('_admin._layout.icons.add')
                Tambah Data
            </x-admin.button>
        </div>
    </x-admin.page-header>

    <div class="mb-6">
        <form action="{{ route('admin.logbook.index') }}" method="GET" navigate-form
            class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-48">
                <x-admin.select :label="null" name="month" size="sm" :value="$month ?? ''" :options="[
                    '' => 'Semua Bulan',
                    '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                    '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                    '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                ]" />
            </div>
            <div class="w-full sm:w-32">
                <x-admin.select :label="null" name="year" size="sm" :value="$year ?? ''" :options="$yearOptions" />
            </div>
            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary">
                    @include('_admin._layout.icons.search')
                    Cari
                </x-admin.button>
                @if (!empty($month) || !empty($year))
                    <x-admin.button href="{{ route('admin.logbook.index') }}" size="sm" color="outline-secondary">
                        @include('_admin._layout.icons.reset')
                        Reset
                    </x-admin.button>
                @endif
            </div>
        </form>
    </div>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Tanggal</x-admin.table.th>
                    <x-admin.table.th>Deskripsi Kegiatan</x-admin.table.th>
                    <x-admin.table.th align="end"></x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $d)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200 whitespace-nowrap">
                                {{ $d['tanggal'] }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm text-gray-800 dark:text-neutral-200">
                                {{ $d['deskripsi'] }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-1.5 flex items-center justify-end gap-x-1">
                            <a href="{{ route('admin.logbook.detail', $d['id']) }}"
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700"
                                title="View">
                                @include('_admin._layout.icons.view_detail')
                            </a>
                            <a href="{{ route('admin.logbook.update', $d['id']) }}"
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:bg-blue-100 disabled:opacity-50 disabled:pointer-events-none dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-500 dark:hover:bg-blue-800/30 dark:hover:border-blue-700"
                                title="Edit">
                                @include('_admin._layout.icons.pencil')
                            </a>
                            <button type="button"
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:bg-red-100 disabled:opacity-50 disabled:pointer-events-none dark:border-red-800 dark:bg-red-900/20 dark:text-red-500 dark:hover:bg-red-800/30 dark:hover:border-red-700 cursor-pointer"
                                title="Delete"
                                data-hs-overlay="#delete-logbook-modal"
                                onclick="setDeleteLogbookAction('{{ route('admin.logbook.delete', $d['id']) }}', '{{ $d['tanggal'] }}')">
                                @include('_admin._layout.icons.trash')
                            </button>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-500">
                            <x-admin.empty-state />
                        </td>
                    </tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>
    </x-admin.table.wrapper>

    <x-admin.confirm-modal
        id="delete-logbook-modal"
        title="Hapus Logbook"
        message="Apakah Anda yakin ingin menghapus logbook ini? Tindakan ini tidak dapat dibatalkan."
        confirmText="Ya, Hapus" />

    @push('scripts')
        <script>
            function setDeleteLogbookAction(url, tanggal) {
                document.getElementById('delete-logbook-modal-form').action = url;
                document.getElementById('delete-logbook-modal-message').textContent =
                    'Apakah Anda yakin ingin menghapus logbook tanggal ' + tanggal + '? Tindakan ini tidak dapat dibatalkan.';
            }
        </script>
    @endpush
@endsection