<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

use OpenApi\Generator;
use OpenApi\Processors\MergeIntoOpenApi;

class ProcGenerator extends DocGenerator
{
    public function docPath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/' . $relativeName;
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
