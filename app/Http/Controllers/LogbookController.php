<?php

namespace App\Http\Controllers;

use App\Constants\ResponseConst;
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
        $endDate = null; //

        if ($month && $year) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $result = $this->logbookUsecase->getAll(Auth::id(), $startDate, $endDate);

        $logbooks = $result['data']['list'] ?? [];

        $data = collect($logbooks)->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
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
        $result = $this->logbookUsecase->getById($id, Auth::id());

        if (!$result['success']) {
            return redirect()->route('admin.logbook.index')->with('error', $result['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $logbook = $result['data']['item'];
        $images = $result['data']['images'];

        return view('_admin.logbook.detail', [
            'logbook' => [
                'id' => $logbook->id,
                'tanggal' => Carbon::parse($logbook->tanggal)->translatedFormat('d F Y'),
                'deskripsi' => $logbook->deskripsi,
            ],
            'images' => $images,
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

        $result = $this->logbookUsecase->getReportData(Auth::id(), $startDate, $endDate);

        if (!$result['success']) {
            return redirect()
                ->route('admin.logbook.index')
                ->with('error', $result['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $report = $result['data']['report'];

        $pdf = Pdf::loadView('_admin.logbook.report-pdf', [
            'report' => $report,
            'user' => Auth::user(),
            'periode' => Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d F Y'),
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
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $result = $this->logbookUsecase->create($validated, Auth::id());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message'] ?? 'Gagal menyimpan data.');
        }

        if ($request->hasFile('images')) {
            $this->logbookUsecase->uploadImages($result['data']['id'], $request->file('images'), Auth::id());
        }

        return redirect()->route('admin.logbook.index')->with('success', ResponseConst::SUCCESS_MESSAGE_CREATED);
    }

    public function update(int $id)
    {
        $result = $this->logbookUsecase->getById($id, Auth::id());

        if(!$result['success']){
            return redirect()->route('admin.logbook.index')->with('error', $result['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $logbook = $result['data']['item'];
        $images = $result['data']['images'];

        return view('_admin.logbook.update', compact('logbook', 'images'));
    }

    public function doUpdate(Request $request, int $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string|max:2000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $result = $this->logbookUsecase->update($id, $validated, Auth::id());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message'] ?? 'Gagal mengupdate data.');
        }

        if ($request->hasFile('images')) {
            $this->logbookUsecase->uploadImages($id, $request->file('images'), Auth::id());
        }

        return redirect()->route('admin.logbook.index')->with('success', ResponseConst::SUCCESS_MESSAGE_UPDATED);
    }

    public function delete(int $id)
    {
        $result = $this->logbookUsecase->delete($id, Auth::id());

        if (!$result['success']) {
            return redirect()
                ->route('admin.logbook.index')
                ->with('error', $result['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()
            ->route('admin.logbook.index')
            ->with('success', ResponseConst::SUCCESS_MESSAGE_DELETED);
    }

    public function deleteImage(int $id)
    {
        $result = $this->logbookUsecase->deleteImage($id, Auth::id());

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return back()->with('success', ResponseConst::SUCCESS_MESSAGE_DELETED);
    }
}
