<?php

namespace App\Http\Controllers;
use App\Models\RanobeYear;
use App\Models\RanobeVolume;
use App\Models\RanobeChapter;
use Illuminate\Support\Facades\DB;

class RanobeController extends Controller
{
    public function index()
    {
        $years = RanobeYear::orderBy('year_number', 'asc')
            ->withCount(['volumes', 'chapters'])
            ->withSum('volumes as total_pages', 'pages_quantity')
            ->get();

        return view('pages.ranobe.index',compact('years'));
    }

        public function showYear(int $year)
    {
        $yearModel = RanobeYear::where('year_number',$year)->firstOrFail();
        $volumes = $yearModel->volumes;
        return view('pages.ranobe.year', compact('year','volumes'));
    }
}