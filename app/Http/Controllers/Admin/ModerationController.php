<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Appeal;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public function appealReject(Request $request, Appeal $appeal)
    {
        $validated = $request->validate([
            'admin_note' => ['required', 'string'],
        ]);
        DB::beginTransaction();
        try {
            $appeal->update([
                'status' => 'rejected',
                'admin_note' => $validated['admin_note'],
            ]);

            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Reject Appeal Request',
                'target_type' => 'Appeal',
                'target_id' => $appeal->id,
                'metadata' => [
                    'reason' => $validated['admin_note'],
                ],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the appeal.');
        }

        return redirect()->back()->with('success', 'Appeal has been rejected.');
    }

    public function appealApprove(Appeal $appeal)
    {
        DB::beginTransaction();

        try {
            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Accept Appeal Request',
                'target_type' => 'Appeal',
                'target_id' => $appeal->id,
                'metadata' => [
                    'previous_status' => $appeal->user->status,
                    'new_status' => 'active',
                ],
            ]);

            $appeal->update(['status' => 'approved']);
            
            User::where('id', $appeal->user_id)->update([
                'status' => 'active',
                'status_updated_at' => now(),
                'reason' => null,
                'suspended_until' => null,
            ]);

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
            switch ($action) {
                case 'suspend':
                    $report->reportedUser->update([
                        'status' => 'suspended',
                        'status_updated_at' => now(),
                        'reason' => $reason,
                        'suspended_until' => now()->addDays(7),
                    ]);
                    $report->update(['status' => 'resolved']);

                    AuditLog::create([
                        'id' => Str::uuid(),
                        'user_id' => Auth::id(),
                        'action' => 'Resolve Suspend Report',
                        'target_type' => 'Report',
                        'target_id' => $report->id,
                        'metadata' => [
                            'action_taken' => 'Suspend User',
                            'reason' => $reason,
                            'duration' => '7 days',
                        ],
                    ]);
                    break;
                case 'ban':
                    $report->reportedUser->update([
                        'status' => 'banned',
                        'status_updated_at' => now(),
                        'reason' => $reason,
                    ]);
                    $report->update(['status' => 'resolved']);

                    AuditLog::create([
                        'id' => Str::uuid(),
                        'user_id' => Auth::id(),
                        'action' => 'Resolve Ban Report',
                        'target_type' => 'Report',
                        'target_id' => $report->id,
                        'metadata' => [
                            'action_taken' => 'Ban User',
                            'reason' => $reason,
                        ],
                    ]);
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
        DB::beginTransaction();

        try {
            $report->update(['status' => 'ignored']);

            AuditLog::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'action' => 'Ignored Report',
                'target_type' => 'Report',
                'target_id' => $report->id,
                'metadata' => [
                    'reason' => 'No violation found',
                ],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the report.');
        }

        return redirect()->back()->with('success', 'Report has been ignored.');
    }
}
