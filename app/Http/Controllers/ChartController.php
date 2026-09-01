<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ChartController extends Controller
{
    public function index() {
        return view('admin.overview');
    }

    public function getDataUsers() {
        $data = User::select(
            DB::raw('COUNT(id) as total_users'),
            DB::raw("DATE_FORMAT(created_at, '%M') as month"),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year")
        )
            ->groupBy('month', 'month_year')
            ->orderBy('month_year', 'asc')
            ->get();

        $months = $data->pluck('month')->toArray();
        $total_users = $data->pluck('total_users')->toArray();

        return response()->json([
            'months' => $months,
            'total_users' => $total_users
        ]);
    }
}