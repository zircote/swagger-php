<?php declare(strict_types=1);

namespace OpenApi\Tools\Docs;

use OpenApi\Generator;
use OpenApi\Processors\MergeIntoOpenApi;

class ProcGenerator
{
    public const NO_DETAILS_AVAILABLE = 'No details available.';

    protected $projectRoot;

    public function __construct($projectRoot)
    {
        $this->projectRoot = realpath($projectRoot);
    }

    public function docPath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/' . $relativeName;
    }

    public function preamble(): string
    {
        return <<< EOT
# Processors

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


EOT;
    }

    public function getProcessorDetails(): array
    {
        $processors = [];

        $defaultProcessors = [];
        foreach ((new Generator())->getProcessors() as $processor) {
            $rc = new \ReflectionClass($processor);
            $class = $rc->getName();
            $properties = [];
            foreach ($rc->getMethods() as $method) {
                if (0 === strpos($method->getName(), 'set')) {
                    $pname = lcfirst(substr($method->getName(), 3));
                    $type = 'n/a';
                    if (1 == count($method->getParameters())) {
                        if ($rt = $method->getParameters()[0]->getType()) {
                            $type = $rt->getName();
                        }

                    }
                    $properties[$pname] = $type;
                }
            }

            $defaultProcessors[] = $class;
            $processors[] = [
                'class' => $class,
                'name' => $rc->getShortName(),
                'default' => true,
                'properties' => $properties,
            ];
        }

        $proccesorsDir = dirname((new \ReflectionClass(MergeIntoOpenApi::class))->getFileName());
        foreach (glob("$proccesorsDir/*.php") as $processor) {
            $class = 'OpenApi\\Processors\\' . pathinfo($processor, PATHINFO_FILENAME);
            if (!in_array($class, $defaultProcessors)) {
                $rc = new \ReflectionClass($class);
                $properties = [];

                $processors[] = [
                    'class' => $rc->getName(),
                    'name' => $rc->getShortName(),
                    'default' => false,
                    'properties' => $properties,
                ];
            }
        }

        return $processors;
    }
}
