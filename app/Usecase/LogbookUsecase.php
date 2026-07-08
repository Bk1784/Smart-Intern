<?php

namespace App\Usecase;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Presenter\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


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
    {//
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

    public function getById(int $id, int $userId): array
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

            $logbook->tanggal = Carbon::parse($logbook->tanggal)->format('Y-m-d');

            $images = DB::table(DatabaseConst::LOGBOOK_IMAGE())
                ->where('logbook_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return Response::buildSuccess(
                [
                    'item' => $logbook,
                    'images' => $images,
                ],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

   public function create(array $data, int $userId): array
    {
        DB::beginTransaction();
        try {
            $id = DB::table(DatabaseConst::LOGBOOK())
                ->insertGetId([
                    'user_id' => $userId,
                    'tanggal' => $data['tanggal'],
                    'deskripsi' => $data['deskripsi'],
                    'created_by' => $userId,
                    'created_at' => now(),
                ]);

            DB::commit();

            return Response::buildSuccessCreated([
                'id' => $id,
            ]);
        } catch (Exception $e) {
            DB::rollback();

            Log::error(
                message: $e->getMessage(),
                context: [
                    'method' => __METHOD__,
                ]
            );

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function update(int $id, array $data, int $userId): array
    {
        DB::beginTransaction();
        try {
            $logbook = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$logbook) {
                DB::rollback();
                return Response::buildErrorService('Data tidak ditemukan', 404);
            }

            DB::table(DatabaseConst::LOGBOOK())
                ->where('id', $id)
                ->update([
                    'tanggal' => $data['tanggal'],
                    'deskripsi' => $data['deskripsi'],
                    'updated_by' => $userId,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return Response::buildSuccess([], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollback();

            Log::error(
                message: $e->getMessage(),
                context: [
                    'method' => __METHOD__,
                ]
            );

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function delete(int $id, int $userId): array
    {
        DB::beginTransaction();
        try {
            $logbook = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$logbook) {
                DB::rollback();
                return Response::buildErrorService('Data tidak ditemukan', 404);
            }

            // Hapus file fisik gambar terkait
            $images = DB::table(DatabaseConst::LOGBOOK_IMAGE())
                ->where('logbook_id', $id)
                ->get();

            foreach ($images as $image) {
                Storage::disk('public')->delete($image->file_path);
            }

            DB::table(DatabaseConst::LOGBOOK_IMAGE())->where('logbook_id', $id)->delete();

            DB::table(DatabaseConst::LOGBOOK())
                ->where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $userId,
                ]);

            DB::commit();

            return Response::buildSuccess([], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollback();
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);
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
        try {
            $workdays = $this->getWorkdays($startDate, $endDate);

            $logbooks = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('user_id', $userId)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get()
                ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

            // Ambil semua gambar sekaligus buat semua logbook di rentang ini
            $logbookIds = $logbooks->pluck('id')->all();

            $images = DB::table(DatabaseConst::LOGBOOK_IMAGE())
                ->whereIn('logbook_id', $logbookIds)
                ->orderBy('created_at')
                ->get()
                ->groupBy('logbook_id');

            $report = [];
            foreach ($workdays as $day) {
                $carbonDay = Carbon::parse($day);
                $entry = $logbooks->get($day);

                $entryImages = [];
                if ($entry && isset($images[$entry->id])) {
                    foreach ($images[$entry->id] as $image) {
                        $fullPath = storage_path('app/public/' . $image->file_path);

                        if (file_exists($fullPath)) {
                            $entryImages[] = [
                                'base64' => base64_encode(file_get_contents($fullPath)),
                                'mime' => mime_content_type($fullPath),
                            ];
                        }
                    }
                }

                $report[] = [
                    'tanggal' => $carbonDay->translatedFormat('l, d F Y'),
                    'deskripsi' => $entry ? $entry->deskripsi : null,
                    'images' => $entryImages,
                ];
            }

            return Response::buildSuccess(
                [
                    'report' => $report,
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

    public function uploadImages(int $logbookId, array $files, int $userId): array
    {
        try {
            $logbook = DB::table(DatabaseConst::LOGBOOK())
                ->whereNull('deleted_at')
                ->where('id', $logbookId)
                ->where('user_id', $userId)
                ->first();

            if (!$logbook) {
                return Response::buildErrorService('Data tidak ditemukan', 404);
            }

            foreach ($files as $file) {
                /** @var UploadedFile $file */
                $path = $file->store('logbook', 'public');

                $this->compressImage(storage_path('app/public/' . $path));

                DB::table(DatabaseConst::LOGBOOK_IMAGE())->insert([
                    'logbook_id' => $logbookId,
                    'file_path' => $path,
                    'created_at' => now(),
                ]);
            }

            return Response::buildSuccess([], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    private function compressImage(string $fullPath): void
    {
        if (!file_exists($fullPath)) {
            return;
        }

        $imageInfo = getimagesize($fullPath);

        if (!$imageInfo) {
            return;
        }

        [$originalWidth, $originalHeight, $imageType] = $imageInfo;

        // Buat resource gambar sesuai tipe filenya
        $source = match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($fullPath),
            default => null,
        };

        if (!$source) {
            return;
        }

        // Hitung ukuran baru, cuma resize kalau lebih besar dari 700px
        $maxWidth = 700;

        if ($originalWidth <= $maxWidth) {
            imagedestroy($source);
            return;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) (($originalHeight / $originalWidth) * $newWidth);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi buat PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // Simpan ulang dengan kualitas 80%, timpa file asli
        match ($imageType) {
            IMAGETYPE_JPEG => imagejpeg($resized, $fullPath, 80),
            IMAGETYPE_PNG => imagepng($resized, $fullPath, 2), // PNG: skala 0-9, ~80% setara level 2
            IMAGETYPE_WEBP => imagewebp($resized, $fullPath, 80),
            default => null,
        };

        imagedestroy($source);
        imagedestroy($resized);
    }

    public function getImages(int $logbookId): array
    {
        try {
            $images = DB::table(DatabaseConst::LOGBOOK_IMAGE())
                ->where('logbook_id', $logbookId)
                ->orderBy('created_at', 'desc')
                ->get();

            return Response::buildSuccess(['items' => $images], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function deleteImage(int $imageId, int $userId): array
    {
        try {
            $image = DB::table(DatabaseConst::LOGBOOK_IMAGE())
                ->join(DatabaseConst::LOGBOOK().' as l', 'l.id', '=', DatabaseConst::LOGBOOK_IMAGE().'.logbook_id')
                ->where(DatabaseConst::LOGBOOK_IMAGE().'.id', $imageId)
                ->where('l.user_id', $userId)
                ->select(DatabaseConst::LOGBOOK_IMAGE().'.*')
                ->first();

            if (!$image) {
                return Response::buildErrorService('Gambar tidak ditemukan', 404);
            }

            Storage::disk('public')->delete($image->file_path);

            DB::table(DatabaseConst::LOGBOOK_IMAGE())->where('id', $imageId)->delete();

            return Response::buildSuccess([], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function generateExcel(array $report, $user, string $periode, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Logbook');

        // Judul
        $sheet->setCellValue('A1', 'Laporan Logbook Aktivitas');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Subjudul
        $sheet->setCellValue('A2', $user->name . ' - Periode: ' . $periode);
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF666666'));

        // Header tabel
        $sheet->setCellValue('A4', 'Hari, Tanggal');
        $sheet->setCellValue('B4', 'Deskripsi Kegiatan');
        $sheet->setCellValue('C4', 'Jumlah Foto');

        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F5F5F5');

        // Isi data
        $row = 5;
        foreach ($report as $item) {
            $sheet->setCellValue('A' . $row, $item['tanggal']);
            $sheet->setCellValue('B' . $row, $item['deskripsi'] ?? 'Tidak ada aktivitas tercatat');
            $sheet->setCellValue('C' . $row, count($item['images'] ?? []));

            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);

            $row++;
        }

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(15);

        // Border seluruh tabel
        $lastRow = $row - 1;
        $sheet->getStyle('A4:C' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
