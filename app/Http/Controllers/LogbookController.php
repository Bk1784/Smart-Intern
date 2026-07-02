<?php

namespace App\Http\Controllers;

use App\Constants\ResponseConst;
use App\Models\Logbook;
use App\Usecase\LogbookUsecase;
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
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $logbooks = $this->logbookUsecase->getAll(Auth::id(), $startDate, $endDate);

        $data = $logbooks->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal->translatedFormat('d F Y'),
                'deskripsi' => Str::limit($item->deskripsi, 50),
            ];
        });

        return view('_admin.logbook.index', compact('data', 'startDate', 'endDate'));
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
