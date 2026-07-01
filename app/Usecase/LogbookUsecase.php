<?php

namespace App\Usecase;

use App\Models\Logbook;
use Illuminate\Database\Eloquent\Collection;

class LogbookUsecase
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAll(int $userId): Collection
    {
        return Logbook::query()->where('user_id', $userId)->orderBy('tanggal', 'desc')->get();
    }

    public function create(array $data, int $userId): Logbook
    {
        return Logbook::create([
            'user_id' => $userId,
            'tanggal' => $data['tanggal'],
            'deskripsi' => $data['deskripsi'],
            'created_by' => $userId
        ]);
    }

    public function getById(int $id, int $userId): ?Logbook
    {
        return Logbook::query()->where('id', $id)->where('user_id', $userId)->first();
    }

    public function update(Logbook $logbook, array $data, int $userId): Logbook
    {
        $logbook->update([
            'tanggal' => $data['tanggal'],
            'deskripsi' => $data['deskripsi'],
            'updated_by' => $userId
        ]);

        return $logbook;
    }
}
