<?php

namespace App\Http\Controllers;
use App\Models\RanobeYear;
use App\Models\RanobeVolume;
use App\Models\RanobeChapter;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Decimal;

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

    public function showVolume(int $year, float $volume)
    {
        $volumeModel = RanobeVolume::query()
            ->select('id', 'volume_description','volume_number','cover_image','cover_image_mobile') 
            ->where('volume_number', $volume)
            ->with(['chapters' => function ($query) {
                $query->select('id', 'ranobe_volume_id', 'title','chapter_number');
            }])
            ->whereHas('year', function ($query) use ($year) {
                $query->where('year_number', $year);
            })
            ->firstOrFail();
        $chapters = $volumeModel->chapters;
        $volume_number_rounded = floatval($volumeModel->volume_number);
        return view('pages.ranobe.volume',compact('year','volumeModel','chapters','volume_number_rounded'));
    }
}