<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index()
    {
         $data = [
            [
                'tanggal' => '01 Juli 2026',
                'deskripsi' => 'Setup environment Laragon dan konfigurasi Drizzle ORM untuk penambahan tabel task_reports.',
            ],
            [
                'tanggal' => '30 Juni 2026',
                'deskripsi' => 'Membuat halaman list Logbook menggunakan komponen admin table yang sudah tersedia.',
            ],
            [
                'tanggal' => '26 Juni 2026',
                'deskripsi' => 'Menambahkan menu sidebar Logbook dan menyesuaikan hak akses.',
            ],
        ];


        return view('_admin.logbook.index', compact('data'));
    }
}
