<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Schedule;

class PetugasDashboardController extends Controller
{
    public function __invoke()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        /*
         * Query dasar:
         * hanya jadwal dari kegiatan yang ditugaskan
         * kepada pengguna yang sedang login.
         */
        $assignedSchedules = Schedule::query()
            ->whereHas('activity.assignments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        /*
         * 1. Jadwal hari ini.
         * Jadwal yang dibatalkan tidak dihitung.
         */
        $todayCount = (clone $assignedSchedules)
            ->whereDate('schedule_date', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        /*
         * 2. Jadwal mendatang.
         */
        $upcomingCount = (clone $assignedSchedules)
            ->whereDate('schedule_date', '>', $today)
            ->whereIn('status', ['scheduled', 'rescheduled'])
            ->count();

        /*
         * 3. Jadwal yang telah selesai.
         */
        $completedCount = (clone $assignedSchedules)
            ->where('status', 'completed')
            ->count();

        /*
         * 4. Kegiatan yang sudah memiliki laporan.
         * Perhitungan dibatasi pada kegiatan yang ditugaskan
         * kepada petugas yang sedang login.
         */
        $reportCount = Activity::query()
            ->whereHas('assignments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('report')
            ->count();

        $summary = [
            'today' => $todayCount,
            'upcoming' => $upcomingCount,
            'completed' => $completedCount,
            'reports' => $reportCount,
        ];

        /*
         * 5. Daftar jadwal tugas petugas.
         * Prioritaskan jadwal hari ini dan mendatang,
         * kemudian jadwal lampau yang paling baru.
         */
        $mySchedules = Schedule::query()
            ->with([
                'activity.activityType',
                'activity.report',
                'location',
            ])
            ->whereHas('activity.assignments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', '!=', 'cancelled')
            ->orderByRaw(
                'CASE WHEN schedule_date < ? THEN 1 ELSE 0 END',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN schedule_date >= ? THEN schedule_date END ASC',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN schedule_date < ? THEN schedule_date END DESC',
                [$today]
            )
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('dashboard.petugas', compact(
            'summary',
            'mySchedules'
        ));
    }
}
