<?php

namespace App\Http\Controllers;

use App\Models\ClassesTop;
use App\Models\UpdateFeed;

class MainController extends Controller
{
    public function index()
    {
        $classes_list_default = ClassesTop::where('spoilers', 0)
            ->orderBy('class_points', 'desc')
            ->get();

        $max_points_default = $classes_list_default->first()?->class_points ?? 0;

        $classes_list_default->transform(function ($class) use ($max_points_default) {
            $class->percent = ($max_points_default > 0) 
                ? min(100, round(($class->class_points / $max_points_default) * 100, 2)) 
                : 0;
            return $class;
        });

        $classes_list_spoilers = ClassesTop::where('spoilers', 1)
            ->orderBy('class_points', 'desc')
            ->get();

        $max_points_spoilers = $classes_list_spoilers->first()?->class_points ?? 0;

        $classes_list_spoilers->transform(function ($class) use ($max_points_spoilers) {
            $class->percent = ($max_points_spoilers > 0) 
                ? min(100, round(($class->class_points / $max_points_spoilers) * 100, 2)) 
                : 0;
            return $class;
        });

        $feed = UpdateFeed::orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('pages.dcote_main', compact(
            'classes_list_default',
            'max_points_default',
            'classes_list_spoilers',
            'max_points_spoilers',
            'feed'
        ));
    }
}