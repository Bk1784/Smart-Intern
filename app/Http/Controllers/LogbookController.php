<?php

namespace App\Http\Controllers;

use App\Constants\ResponseConst;
use App\Models\Logbook;
use App\Usecase\LogbookUsecase;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class LogbookController extends Controller
{
    public function __construct(
        protected LogbookUsecase $logbookUsecase
    ) {}

    public function index(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $startDate = null;
        $endDate = null;

        if ($month && $year) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $logbooks = $this->logbookUsecase->getAll(Auth::id(), $startDate, $endDate);

        $data = $logbooks->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal->translatedFormat('d F Y'),
                'deskripsi' => Str::words($item->deskripsi, 30, '...'),
            ];
        });

        $yearOptions = ['' => 'Tahun'];
        $currentYear = now()->year;
        for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) {
            $yearOptions[$y] = (string) $y;
        }

        return view('_admin.logbook.index', compact('data', 'month', 'year', 'yearOptions'));
    }

    public function detail(int $id)
    {
        $logbook = $this->logbookUsecase->findById($id, Auth::id());

        return view('_admin.logbook.detail', [
            'logbook' => [
                'id' => $logbook->id,
                'tanggal' => $logbook->tanggal->translatedFormat('d F Y'),
                'deskripsi' => $logbook->deskripsi,
            ],
        ]);
    }

        public function download(Request $request)
    {
        $type = $request->query('type', 'custom');

        switch ($type) {
            case 'weekly':
                $request->validate(['week' => 'required']);
                [$startDate, $endDate] = $this->logbookUsecase->getWeekRange($request->query('week'));
                $label = 'Mingguan';
                break;

            case 'monthly':
                $request->validate(['month' => 'required']);
                [$startDate, $endDate] = $this->logbookUsecase->getMonthRange($request->query('month'));
                $label = 'Bulanan';
                break;

            default:
                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ]);
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');
                $label = 'Custom';
                break;
        }

        $report = $this->logbookUsecase->getReportData(Auth::id(), $startDate, $endDate);

        $pdf = Pdf::loadView('_admin.logbook.report-pdf', [
            'report' => $report,
            'user' => Auth::user(),
            'periode' => \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y'),
        ]);

        $filename = 'logbook-' . Str::slug($label) . '-' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function add()
    {
        return view('_admin.logbook.add');
    }

    public function doCreate(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:2000',
        ]);

        $this->logbookUsecase->create($validated, Auth::id());

        return redirect()->route('admin.logbook.index')->with('success', ResponseConst::SUCCESS_MESSAGE_CREATED);
    }

    public function update(int $id)
    {
        $logbook = $this->logbookUsecase->getById($id, Auth::id());

        return view('_admin.logbook.update', compact('logbook'));
    }

    public function doUpdate(Request $request, int $id)
    {
        $logbook = $this->logbookUsecase->getById($id, Auth::id());

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:2000',
        ]);

        $this->logbookUsecase->update($logbook, $validated, Auth::id());

        return redirect()->route('admin.logbook.index')->with('success', ResponseConst::SUCCESS_MESSAGE_UPDATED);
    }

    public function delete(int $id)
    {
        $this->logbookUsecase->delete($id, Auth::id());

        return redirect()->route('admin.logbook.index')->with('success', ResponseConst::SUCCESS_MESSAGE_DELETED);
    }
}
