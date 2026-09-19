<?php

namespace Tests\Feature;

use App\Models\ClassesTop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassesTopRankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_assigns_class_letters_by_points_within_each_spoiler_group(): void
    {
        ClassesTop::query()->delete();

        $defaultClasses = collect([
            ClassesTop::create(['leader' => 'Default C', 'class_points' => 300, 'spoilers' => false]),
            ClassesTop::create(['leader' => 'Default A', 'class_points' => 500, 'spoilers' => false]),
            ClassesTop::create(['leader' => 'Default D', 'class_points' => 200, 'spoilers' => false]),
            ClassesTop::create(['leader' => 'Default B', 'class_points' => 400, 'spoilers' => false]),
        ]);

        $spoilerClasses = collect([
            ClassesTop::create(['leader' => 'Spoiler D', 'class_points' => 10, 'spoilers' => true]),
            ClassesTop::create(['leader' => 'Spoiler B', 'class_points' => 30, 'spoilers' => true]),
            ClassesTop::create(['leader' => 'Spoiler A', 'class_points' => 40, 'spoilers' => true]),
            ClassesTop::create(['leader' => 'Spoiler C', 'class_points' => 20, 'spoilers' => true]),
        ]);

        $this->assertSame(
            ['A', 'B', 'C', 'D'],
            $defaultClasses->sortByDesc('class_points')->map(fn (ClassesTop $class) => $class->fresh()->letter)->values()->all()
        );
        $this->assertSame(
            ['A', 'B', 'C', 'D'],
            $spoilerClasses->sortByDesc('class_points')->map(fn (ClassesTop $class) => $class->fresh()->letter)->values()->all()
        );

        $lowestDefaultClass = $defaultClasses->sortBy('class_points')->first();
        $lowestDefaultClass->update(['class_points' => 600]);

        $this->assertSame('A', $lowestDefaultClass->fresh()->letter);
        $this->assertSame(
            ['A', 'B', 'C', 'D'],
            ClassesTop::query()
                ->where('spoilers', false)
                ->orderByDesc('class_points')
                ->pluck('letter')
                ->all()
        );
    }

    public function test_it_rebalances_both_groups_when_spoiler_state_changes(): void
    {
        ClassesTop::query()->delete();

        $movingClass = ClassesTop::create([
            'leader' => 'Moving class',
            'class_points' => 300,
            'spoilers' => false,
        ]);
        $remainingDefaultClass = ClassesTop::create([
            'leader' => 'Default class',
            'class_points' => 100,
            'spoilers' => false,
        ]);
        $highestSpoilerClass = ClassesTop::create([
            'leader' => 'Highest spoiler class',
            'class_points' => 400,
            'spoilers' => true,
        ]);
        $lowestSpoilerClass = ClassesTop::create([
            'leader' => 'Lowest spoiler class',
            'class_points' => 200,
            'spoilers' => true,
        ]);

        $movingClass->update(['spoilers' => true]);

        $this->assertSame('A', $remainingDefaultClass->fresh()->letter);
        $this->assertSame('A', $highestSpoilerClass->fresh()->letter);
        $this->assertSame('B', $movingClass->fresh()->letter);
        $this->assertSame('C', $lowestSpoilerClass->fresh()->letter);
    }
}
