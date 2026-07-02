<?php

namespace App\Usecase;

use App\Constants\ResponseConst;
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

    public function getAll(int $userId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Logbook::query()
            ->where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function findById(int $id, int $userId): ?Logbook
    {
        return Logbook::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
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

    public function delete(int $id, int $userId): bool
    {
        $logbook = Logbook::query()->where('id', $id)->where('user_id', $userId)->first();

        return $logbook->delete();
    }
}
