<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnimeReleasedEpisodesProgressTest extends TestCase
{
    use DatabaseTransactions;

    public function test_episode_separates_player_video_and_metrics_origins(): void
    {
        config()->set('services.dcote.video_base_url', 'https://video.example');
        config()->set('services.dcote.metrics_base_url', 'https://metrics.example');

        $season = AnimeSeason::create([
            'season_number' => (int) AnimeSeason::query()->max('season_number') + 100,
            'number_of_episodes' => 1,
        ]);
        $episode = AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => 1,
            'episode_name' => 'Player routing test',
            'completed' => true,
            'opening_start' => -1,
        ]);

        $response = $this->get(route('anime.episode', [
            'season' => $season->season_number,
            'episode' => $episode->episode_number,
        ]));

        $response->assertOk();
        $response->assertSee(
            'https://metrics.example/site-presence-tracker.js',
            false,
        );

        $playerUrl = parse_url($response->viewData('episodeUrl'));
        parse_str($playerUrl['query'], $playerQuery);

        $this->assertSame('video.example', $playerUrl['host']);
        $this->assertSame('/videoplayer', $playerUrl['path']);
        $this->assertSame(
            'https://video.example/season-0'.$season->season_number
                .'/episode-01/master.m3u8',
            $playerQuery['src'],
        );
    }

    public function test_anime_index_counts_only_completed_episodes_as_released(): void
    {
        $season = AnimeSeason::create([
            'season_number' => (int) AnimeSeason::query()->max('season_number') + 100,
            'number_of_episodes' => 2,
        ]);

        AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => 1,
            'episode_name' => 'Released episode',
            'completed' => true,
            'opening_start' => -1,
        ]);

        AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => 2,
            'episode_name' => 'Upcoming episode',
            'completed' => false,
            'opening_start' => -1,
            'appear_in' => now()->addDay(),
        ]);

        $response = $this->get(route('anime.index'));

        $response->assertOk();

        $seasonFromView = $response->viewData('seasons_list')
            ->firstWhere('id', $season->id);

        $this->assertNotNull($seasonFromView);
        $this->assertSame(1, $seasonFromView->released_episodes_count);
    }
}
