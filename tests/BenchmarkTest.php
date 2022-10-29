<?php

use markhuot\craftpest\factories\Entry;
use markhuot\craftpest\factories\Section;

it('benchmarks duplicate queries')
    ->beginBenchmark()
    ->get('/')
    ->assertOk()
    ->endBenchmark()
    ->assertNoDuplicateQueries();

it('benchmarks load time')
    ->beginBenchmark()
    ->get('/')
    ->assertOk()
    ->endBenchmark()
    ->assertLoadTimeLessThan(2);

it('benchmarks', function () {
    $section = Section::factory()->template('entry')->create();
    $entry = Entry::factory()->section($section)->create();

    $this->beginBenchmark()
        ->get($entry->uri)
        ->assertOk()
        ->endBenchmark()
        ->assertNoDuplicateQueries()
        ->assertLoadTimeLessThan(1)
        ->assertMemoryLoadLessThan(2048)
        ->assertAllQueriesFasterThan(0.5);
})->skip();