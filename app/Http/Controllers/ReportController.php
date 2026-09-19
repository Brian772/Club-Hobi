<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate([
                'contentType' => ['required', 'in:user,post,comment'],
                'contentId' => ['required', 'uuid'],
                'reason' => ['required', 'string'],
            ]);

            $type = $request->contentType;
            $id = $request->contentId;

            $reportedContent = match ($type) {
                'user' => User::findOrFail($id),
                'post' => Post::findOrFail($id),
                'comment' => Comment::findOrFail($id),
            };

            $reportedUserId = match ($type) {
                'user' => $reportedContent->id,
                'post' => $reportedContent->user_id,
                'comment' => $reportedContent->user_id,
            };

            Report::create([
                'id' => Str::uuid(),
                'reporter_id' => Auth::user()->id,
                'reported_user_id' => $reportedUserId,
                'content_type' => $type,
                'content_id' => $reportedContent->id,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Report berhasil dikirim!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat report');
        }
    }
}
