<?php

namespace App\Http\Controllers;
use App\Models\RanobeYear;
use App\Models\RanobeVolume;
use App\Models\RanobeChapter;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Decimal;
use Illuminate\Support\Str;
use App\Helpers\MarkdownRanobeHelper;
use App\Helpers\FilesCollectionHelper;
use Illuminate\Support\Facades\Storage;

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
        $volumes = $yearModel->volumes()->orderBy('volume_number','desc')->get();
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
        $chapters = $volumeModel->chapters()->orderBy('chapter_number','asc')->get();
        $volume_number_rounded = floatval($volumeModel->volume_number);
        $path = "ranobe/year-$year/volume-$volume_number_rounded/images/";

        $color_images = FilesCollectionHelper::findFiles($path,'-color','public');
        $bw_images = FilesCollectionHelper::findFiles($path,'-bw','public');


        return view('pages.ranobe.volume',compact('year','volumeModel','chapters','volume_number_rounded','color_images','bw_images','path'));
    }


    public function showChapter(int $year, float $volume, float $chapter)
    {
        $chapterModel = RanobeChapter::query()
            ->where('chapter_number', $chapter)
            ->whereHas('year', fn($q) => $q->where('year_number', $year))
            ->whereHas('volume', fn($q) => $q->where('volume_number', $volume))
            ->with('volume')
            ->firstOrFail();

        $prev_chapter = $this->getPreviousChapter($chapterModel);
        $prev_link = $prev_chapter ? route('ranobe.chapter', [
            'year' => $prev_chapter->year->year_number,
            'volume' => floatval($prev_chapter->volume->volume_number),
            'chapter' => floatval($prev_chapter->chapter_number)
        ]) : null;
        $next_chapter = $this->getNextChapter($chapterModel);
        $next_link = $next_chapter ? route('ranobe.chapter', [
            'year' => $next_chapter->year->year_number,
            'volume' => floatval($next_chapter->volume->volume_number),
            'chapter' => floatval($next_chapter->chapter_number)
        ]) : null;
        $content = $chapterModel->chapter_content;
        $htmlContent = MarkdownRanobeHelper::parse($content,$year,$volume);
        $volume_number_rounded = floatval($volume);
        return view('pages.ranobe.chapter', compact('htmlContent','chapterModel','volume_number_rounded','year','chapter',
        'prev_link','next_link'));
    }

    private function getPreviousChapter(RanobeChapter $chapterModel): ?RanobeChapter
        {
            $prev = RanobeChapter::where('ranobe_volume_id', $chapterModel->ranobe_volume_id)
                ->where('chapter_number', '<', $chapterModel->chapter_number)
                ->orderBy('chapter_number', 'desc')
                ->first();

            if ($prev) return $prev; 
            $prevVolume = RanobeVolume::where('ranobe_year_id', $chapterModel->ranobe_year_id)
                ->where('volume_number', '<', $chapterModel->volume->volume_number)
                ->orderBy('volume_number', 'desc')
                ->first();

            if ($prevVolume) {
                $prev = RanobeChapter::where('ranobe_volume_id', $prevVolume->id)
                    ->orderBy('chapter_number', 'desc')
                    ->first();
                
                if ($prev) return $prev;
            }

            $prevYear = RanobeYear::where('year_number', '<', $chapterModel->year->year_number)
                ->orderBy('year_number', 'desc')
                ->first();

            if ($prevYear) {
                $lastVolumeOfPrevYear = RanobeVolume::where('ranobe_year_id', $prevYear->id)
                    ->orderBy('volume_number', 'desc')
                    ->first();

                if ($lastVolumeOfPrevYear) {
                    return RanobeChapter::where('ranobe_volume_id', $lastVolumeOfPrevYear->id)
                        ->orderBy('chapter_number', 'desc')
                        ->first();
                }
            }

            return null;
        }

    private function getNextChapter(RanobeChapter $chapterModel): ?RanobeChapter
        {
            $next = RanobeChapter::where('ranobe_volume_id', $chapterModel->ranobe_volume_id)
                ->where('chapter_number', '>', $chapterModel->chapter_number)
                ->orderBy('chapter_number', 'asc')
                ->first();

            if ($next) return $next; 
            $nextVolume = RanobeVolume::where('ranobe_year_id', $chapterModel->ranobe_year_id)
                ->where('volume_number', '>', $chapterModel->volume->volume_number)
                ->orderBy('volume_number', 'asc')
                ->first();

            if ($nextVolume) {
                $next = RanobeChapter::where('ranobe_volume_id', $nextVolume->id)
                    ->orderBy('chapter_number', 'asc')
                    ->first();
                
                if ($next) return $next;
            }

            $nextYear = RanobeYear::where('year_number', '>', $chapterModel->year->year_number)
                ->orderBy('year_number', 'asc')
                ->first();

            if ($nextYear) {
                $firstVolumeOfNextYear = RanobeVolume::where('ranobe_year_id', $nextYear->id)
                    ->orderBy('volume_number', 'asc')
                    ->first();

                if ($firstVolumeOfNextYear) {
                    return RanobeChapter::where('ranobe_volume_id', $firstVolumeOfNextYear->id)
                        ->orderBy('chapter_number', 'asc')
                        ->first();
                }
            }

            return null;
        }
}