<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    public function index()
    {
        $reports = Report::orderBy('created_at', 'desc')
            ->get();

        return view("admin.moderation.index", compact('reports'));
    }

    public function show(Report $report)
    {
        return view("admin.moderation.show", compact('report'));
    }

    public function resolved(Request $request, Report $report)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:suspend,ban'],
            'reason' => ['required', 'string'],
        ]);

        $action = $validated['action'];
        $reason = $validated['reason'];


        DB::beginTransaction();

        try {
            switch ($action) {
                case 'suspend':
                    $report->reportedUser->update([
                        'status' => 'suspended',
                        'reason' => $reason,
                        'suspended_until' => now()->addDays(7),
                    ]);
                    $report->update(['status' => 'resolved']);
                    break;
                case 'ban':
                    $report->reportedUser->update([
                        'status' => 'banned',
                        'reason' => $reason,
                    ]);
                    $report->update(['status' => 'resolved']);
                    break;
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the report.');
        }

        return redirect()->back()->with('success', 'Report has been resolved.');
    }

    public function ignored(Report $report)
    {
        $report->update(['status' => 'ignored']);

        return redirect()->back()->with('success', 'Report has been ignored.');
    }
}
