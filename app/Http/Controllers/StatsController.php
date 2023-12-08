<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StatsController extends Controller
{
    public function index(): Collection
    {
        $postStatsArray = [];
        $today = Carbon::now()->toDateString();
        foreach (range(0, 23) as $hour) {
            $postStatsArray[$hour] = [
                'hours_start' => $today . ' ' . Str::padLeft($hour, 2, "0") . ":00:00",
                'hour_end'    => $today . ' ' . Str::padLeft($hour, 2, "0") . ":59:59",
                'posts'       => 0,
                'comments'    => 0
            ];
        }

        Post::query()
            ->selectRaw("HOUR(created_at) as grouped_hour, COUNT(id) as hour_count")
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay())
            ->groupByRaw("HOUR(created_at)")
            ->get()
            ->each(function ($stats) use (&$postStatsArray) {
                $postStatsArray[$stats->grouped_hour]['posts'] = $stats->hour_count;
            });

        Comment::query()
               ->selectRaw("HOUR(created_at) as grouped_hour, COUNT(id) as hour_count")
               ->where('created_at', '>=', Carbon::now()->startOfDay())
               ->where('created_at', '<=', Carbon::now()->endOfDay())
               ->groupByRaw("HOUR(created_at)")
               ->get()
               ->each(function ($stats) use (&$postStatsArray) {
                   $postStatsArray[$stats->grouped_hour]['comments'] = $stats->hour_count;
               });


        return collect($postStatsArray)->mapWithKeys(function ($hourData) {
            return ["{$hourData['hours_start']} - {$hourData['hour_end']}" => ['posts' => $hourData['posts'], 'comments' => $hourData['comments']]];
        });
    }
}
