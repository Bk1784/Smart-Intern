<?php

namespace App\Usecase;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Models\Logbook;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Presenter\Response;


class LogbookUsecase
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAll(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $query = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('user_id', $userId)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->when($startDate && !$endDate, function ($query) use ($startDate) {
                    return $query->where('tanggal', '>=', $startDate);
                })
                ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                    return $query->where('tanggal', '<=', $endDate);
                })
                ->orderBy('tanggal', 'desc');

            $data = $query->get();

            return Response::buildSuccess(
                [
                    'list' => $data,
                ],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(
                message: $e->getMessage(),
                context: [
                    'method' => __METHOD__,
                ]
            );

            return Response::buildErrorService($e->getMessage());
        }
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

    public function delete(int $id, int $userId): array
    {
        try {
            $logbook = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$logbook) {
                return Response::buildErrorService('Data tidak ditemukan', 404);
            }

            DB::table(DatabaseConst::LOGBOOK())
                ->where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $userId,
                ]);

            return Response::buildSuccess([], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(
                message: $e->getMessage(),
                context: [
                    'method' => __METHOD__,
                ]
            );

            return Response::buildErrorService($e->getMessage());
        }
    }

      /**
     * Hitung tanggal Senin s/d Jumat dari input minggu (format: "2026-W27")
     */
    public function getWeekRange(string $weekInput): array
    {
        [$year, $week] = explode('-W', $weekInput);

        $monday = Carbon::now()->setISODate((int) $year, (int) $week, 1);
        $friday = $monday->copy()->addDays(4);

        return [$monday->format('Y-m-d'), $friday->format('Y-m-d')];
    }

    /**
     * Hitung tanggal awal s/d akhir dari input bulan (format: "2026-06")
     */
    public function getMonthRange(string $monthInput): array
    {
        $date = Carbon::createFromFormat('Y-m', $monthInput);

        return [
            $date->copy()->startOfMonth()->format('Y-m-d'),
            $date->copy()->endOfMonth()->format('Y-m-d'),
        ];
    }

    /**
     * Generate daftar tanggal kerja (Senin-Jumat, bukan hari libur) dalam rentang
     */
    public function getWorkdays(string $startDate, string $endDate): array
    {
        $holidays = array_keys(config('holidays', []));

        $period = CarbonPeriod::create($startDate, $endDate);

        $workdays = [];
        foreach ($period as $date) {
            $isWeekday = $date->isWeekday(); // Senin-Jumat = true, Sabtu/Minggu = false
            $isHoliday = in_array($date->format('Y-m-d'), $holidays);

            if ($isWeekday && !$isHoliday) {
                $workdays[] = $date->format('Y-m-d');
            }
        }

        return $workdays;
    }

    /**
     * Susun laporan: tiap hari kerja dipasangin sama logbook (kalau ada), atau kosong
     */
    public function getReportData(int $userId, string $startDate, string $endDate): array
    {
        $workdays = $this->getWorkdays($startDate, $endDate);

        $logbooks = Logbook::query()
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn ($item) => $item->tanggal->format('Y-m-d'));

        $report = [];
        foreach ($workdays as $day) {
            $carbonDay = Carbon::parse($day);
            $entry = $logbooks->get($day);

            $report[] = [
                'tanggal' => $carbonDay->translatedFormat('l, d F Y'),
                'deskripsi' => $entry ? $entry->deskripsi : null,
            ];
        }

        return $report;
    }
}
