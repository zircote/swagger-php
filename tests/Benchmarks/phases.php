<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

/*
 * Prints where a build spends its time, by phase and augmenter, for one generated shape.
 *
 *   composer bench:phases -- [schemas] [depth]
 *
 * Each column is the median of five builds after a warm-up, in milliseconds.
 */

use OpenApi\Builder\Mode;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use OpenApi\Tests\Benchmarks\Timing\TimingBuilder;

require __DIR__ . '/../../vendor/autoload.php';

$shape = new Shape(schemas: (int) ($argv[1] ?? 100), depth: (int) ($argv[2] ?? 2));
$variants = [
    'spec' => [Flavour::Spec, Mode::SPEC, true],
    'spec seeded' => [Flavour::Spec, Mode::SPEC, false],
    'hybrid' => [Flavour::Classic, Mode::HYBRID, true],
];

$rows = [];
$order = [];
foreach ($variants as $label => [$flavour, $mode, $scan]) {
    $fixture = (new FixtureWriter())->write($shape, $flavour);
    $fixture->build($mode, scan: $scan);

    $runs = [];
    for ($run = 0; $run < 5; ++$run) {
        $builder = new TimingBuilder();
        $fixture->build($mode, scan: $scan, builder: $builder);
        $runs[] = $builder->timings();
    }

    // hybrid runs every phase the others do, and one more
    if (count($runs[0]) > count($order)) {
        $order = array_keys($runs[0]);
    }

    foreach (array_keys($runs[0]) as $phase) {
        $values = array_column($runs, $phase);
        sort($values);
        $rows[$phase][$label] = $values[2];
    }
}

printf("%s\n\n%-32s%s\n", $shape->key(), 'phase', implode('', array_map(static fn (string $label): string => sprintf('%14s', $label), array_keys($variants))));
foreach ($order as $phase) {
    printf('%-32s', $phase);
    foreach (array_keys($variants) as $label) {
        printf('%14s', isset($rows[$phase][$label]) ? sprintf('%.1f', $rows[$phase][$label]) : '-');
    }
    echo "\n";
}
