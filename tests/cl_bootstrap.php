<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Tests\Concerns\UsesExamples;

(new class () {
    use UsesExamples;

    public function __invoke(): void
    {
        $this->registerExampleClassloader('petstore');
    }
})();
