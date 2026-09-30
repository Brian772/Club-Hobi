<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appeal;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    public function index()
    {
        $reports = Report::orderBy('created_at', 'desc')
            ->get();

        $appeals = Appeal::orderBy('created_at', 'desc')->get();

        return view("admin.moderation.index", compact('reports', 'appeals'));
    }

    public function appeal(Appeal $appeal)
    {
        $user = User::where('id', $appeal->user_id)->first();
        return view("admin.moderation.appeal", compact('appeal', 'user'));
    }


    public function show(Report $report)
    {
        return view("admin.moderation.show", compact('report'));
    }

    public function appealReject(Appeal $appeal)
    {
        $appeal->update(['status' => 'rejected']);
        Notification::createForUser(
            $appeal->user_id,
            'Banding ditolak',
            'Banding akunmu tidak disetujui.',
            'account_status',
            $appeal->id
        );
        return redirect()->back()->with('success', 'Appeal has been rejected.');
    }

    public function appealApprove(Appeal $appeal)
    {
        DB::beginTransaction();

        try {
            $appeal->update(['status' => 'approved']);
            User::where('id', $appeal->user_id)->update([
                'status' => 'active',
                'reason' => null,
                'suspended_until' => null,
            ]);
            Notification::createForUser(
                $appeal->user_id,
                'Akun dipulihkan',
                'Bandingmu disetujui dan status akunmu telah dipulihkan.',
                'account_status',
                $appeal->id
            );
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the report.');
        }
        return redirect()->back()->with('success', 'Appeal has been approved.');
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
            $reportedUser = $report->reportedUser;
            switch ($action) {
                case 'suspend':
                    $reportedUser->update([
                        'status' => 'suspended',
                        'reason' => $reason,
                        'suspended_until' => now()->addDays(7),
                    ]);
                    $report->update(['status' => 'resolved']);
                    break;
                case 'ban':
                    $reportedUser->update([
                        'status' => 'banned',
                        'reason' => $reason,
                    ]);
                    $report->update(['status' => 'resolved']);
                    break;
            }

            Notification::createForUser(
                $reportedUser->id,
                'Status akun diperbarui',
                $action === 'suspend'
                    ? 'Akunmu ditangguhkan selama 7 hari: ' . $reason
                    : 'Akunmu telah diblokir: ' . $reason,
                'account_status',
                $report->id
            );
            Notification::createForUser(
                $report->reporter_id,
                'Laporan ditindaklanjuti',
                'Laporan yang kamu kirim telah ditindaklanjuti.',
                'report',
                $report->id
            );

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
        Notification::createForUser(
            $report->reporter_id,
            'Laporan ditinjau',
            'Laporan yang kamu kirim telah ditinjau.',
            'report',
            $report->id
        );

        return redirect()->back()->with('success', 'Report has been ignored.');
    }
}
