<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        /*
         * 1. Menghitung jumlah kegiatan berdasarkan status
         */
        $statusCounts = Activity::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total' => Activity::count(),
            'planned' => (int) $statusCounts->get('planned', 0),
            'scheduled' => (int) $statusCounts->get('scheduled', 0),
            'ongoing' => (int) $statusCounts->get('ongoing', 0),
            'completed' => (int) $statusCounts->get('completed', 0),
            'cancelled' => (int) $statusCounts->get('cancelled', 0),
        ];

        /*
         * 2. Mengambil lima kegiatan terbaru
         */

        $today = now()->toDateString();

        $latestActivities = Activity::query()
            ->with([
                'activityType',

                'schedules' => function ($query) use ($today) {
                    $query->with('location')
                        ->where('status', '!=', 'cancelled')

                        // Prioritaskan jadwal hari ini atau mendatang.
                        ->orderByRaw(
                            'CASE WHEN schedule_date >= ? THEN 0 ELSE 1 END',
                            [$today]
                        )

                        // Pilih jadwal mendatang yang paling dekat.
                        ->orderByRaw(
                            'CASE WHEN schedule_date >= ? THEN schedule_date END ASC',
                            [$today]
                        )

                        // Jika tidak ada jadwal mendatang,
                        // pilih jadwal sebelumnya yang paling baru.
                        ->orderByRaw(
                            'CASE WHEN schedule_date < ? THEN schedule_date END DESC',
                            [$today]
                        )

                        ->orderBy('start_time');
                },
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();


        /*
         * 3. Menghitung catatan kegiatan selama enam bulan terakhir.
         * Perhitungan memakai created_at, bukan tanggal pelaksanaan.
         */

        $startMonth = now()
            ->startOfMonth()
            ->subMonths(5);

        $endMonth = now()->endOfMonth();

        /*
         * Menghitung jadwal berdasarkan schedule_date
         * selama enam bulan kalender terakhir.
         *
         * Jadwal berstatus cancelled tidak dihitung.
         */
        $monthlyCounts = DB::table('schedules')
            ->selectRaw(
                "DATE_FORMAT(schedule_date, '%Y-%m') as period,
        COUNT(*) as total"
            )
            ->whereBetween('schedule_date', [
                $startMonth->toDateString(),
                $endMonth->toDateString(),
            ])
            ->where('status', '!=', 'cancelled')
            ->groupBy('period')
            ->pluck('total', 'period');


        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $monthlyTrend = collect(range(0, 5))
            ->map(function ($offset) use ($startMonth, $monthlyCounts, $monthNames) {
                $date = $startMonth->copy()->addMonths($offset);
                $period = $date->format('Y-m');

                return [
                    'period' => $period,
                    'label' => $monthNames[(int) $date->format('n')],
                    'total' => (int) $monthlyCounts->get($period, 0),
                ];
            });

        /*
         * 4. Menyiapkan koordinat grafik garis SVG
         */
        $maxCount = (int) ($monthlyTrend->max('total') ?? 0);

        $chartMax = max(
            5,
            (int) (ceil($maxCount / 5) * 5)
        );

        $xPositions = [50, 145, 240, 335, 430, 525];

        $chartPoints = $monthlyTrend
            ->values()
            ->map(function ($item, $index) use ($chartMax, $xPositions) {
                $y = 180 - (
                    ($item['total'] / $chartMax) * 140
                );

                return [
                    'x' => $xPositions[$index],
                    'y' => round($y, 2),
                    'label' => $item['label'],
                    'total' => $item['total'],
                ];
            });

        $linePath = $chartPoints
            ->map(function ($point, $index) {
                $command = $index === 0 ? 'M' : 'L';

                return $command . ' '
                    . $point['x'] . ' '
                    . $point['y'];
            })
            ->implode(' ');

        $areaPath = '';

        if ($chartPoints->isNotEmpty()) {
            $firstPoint = $chartPoints->first();
            $lastPoint = $chartPoints->last();

            $areaPath =
                'M ' . $firstPoint['x'] . ' 180 '
                . $chartPoints->map(
                    fn($point) => 'L '
                        . $point['x'] . ' '
                        . $point['y']
                )->implode(' ')
                . ' L ' . $lastPoint['x'] . ' 180 Z';
        }


        $chartTicks = collect(range(0, 5))
            ->map(function ($step) use ($chartMax) {
                return [
                    'label' => (int) round(
                        $chartMax - ($chartMax / 5 * $step)
                    ),
                    'y' => 40 + ($step * 28),
                ];
            });


        /*
         * 5. Menghitung distribusi berdasarkan jenis kegiatan
         */
        $typeCounts = Activity::query()
            ->join(
                'activity_types',
                'activities.activity_type_id',
                '=',
                'activity_types.id'
            )
            ->select(
                'activity_types.id',
                'activity_types.name',
                DB::raw('COUNT(activities.id) as total')
            )
            ->groupBy(
                'activity_types.id',
                'activity_types.name'
            )
            ->orderByDesc('total')
            ->get();

        $totalByType = (int) $typeCounts->sum('total');

        $typeColors = [
            '#25396B',
            '#149CC3',
            '#F2B82F',
            '#A9B5C7',
            '#D92938',
        ];

        $runningPercentage = 0.0;

        $typeDistribution = $typeCounts
            ->values()
            ->map(function ($type, $index) use ($totalByType, $typeColors, &$runningPercentage) {
                $percentage = $totalByType > 0
                    ? ($type->total / $totalByType) * 100
                    : 0;

                $start = $runningPercentage;
                $runningPercentage += $percentage;

                return [
                    'name' => $type->name,
                    'total' => (int) $type->total,
                    'percentage' => round($percentage, 1),
                    'color' => $typeColors[
                        $index % count($typeColors)
                    ],
                    'start' => round($start, 2),
                    'end' => round($runningPercentage, 2),
                ];
            });

        $donutGradient = $typeDistribution->isEmpty()
            ? '#E2E8F0'
            : 'conic-gradient('
            . $typeDistribution->map(
                fn($type) => $type['color']
                    . ' ' . $type['start'] . '%'
                    . ' ' . $type['end'] . '%'
            )->implode(', ')
            . ')';

        /*
         * 6. Menerjemahkan status database menjadi label UI
         */
        $statusMeta = [
            'planned' => [
                'label' => 'Direncanakan',
                'class' => 'admin-status-planned',
            ],
            'scheduled' => [
                'label' => 'Terjadwal',
                'class' => 'admin-status-scheduled',
            ],
            'ongoing' => [
                'label' => 'Berlangsung',
                'class' => 'admin-status-ongoing',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'admin-status-completed',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'admin-status-cancelled',
            ],
        ];

        return view('dashboard.admin', compact(
            'summary',
            'latestActivities',
            'monthlyTrend',
            'chartMax',
            'chartPoints',
            'chartTicks',
            'linePath',
            'areaPath',
            'typeDistribution',
            'donutGradient',
            'statusMeta'
        ));
    }
}