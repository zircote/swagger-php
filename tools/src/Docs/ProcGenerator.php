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

    protected function getOptionsDetails(\ReflectionClass $rc): array
    {
        $options = [];
        foreach ($rc->getMethods() as $method) {
            if (0 === strpos($method->getName(), 'set')) {
                $pname = lcfirst(substr($method->getName(), 3));
                $type = 'n/a';
                if (1 == count($method->getParameters())) {
                    if ($rt = $method->getParameters()[0]->getType()) {
                        $type = $rt->getName();
                    }
                }

                $phpdoc = $this->extractDocumentation($method->getDocComment());
                $optiondoc = array_key_exists($pname, $phpdoc['params']) ? $phpdoc['params'][$pname] : [];
                if ($phpdoc['content']) {
                    // use method content rather than param if exists
                    $optiondoc['content'] = $phpdoc['content'];
                }

                // default is set on the constructor only
                $rp = null;
                if ($rc->hasMethod('__construct')) {
                    $cc = $rc->getMethod('__construct');
                    foreach ($cc->getParameters() as $parameter) {
                        if ($parameter->getName() === $pname) {
                            $rp = $parameter;
                            break;
                        }
                    }
                }
                $default = 'N/A';
                if ($rp) {
                    $dv = $rp->getDefaultValue();
                    $default = match (gettype($dv)) {
                        'NULL' => 'null',
                        'boolean' => $dv ? 'true' : 'false',
                        'array' => '[' . implode(', ', $dv) . ']',
                    };
                }

                $options[$pname] = [
                    'type' => $type,
                    'phpdoc' => $optiondoc,
                    'default' => $default,
                ];
            }
        }

        return $options;
    }

    public function getProcessorsDetails(): array
    {
        $processors = [];

        $defaultProcessors = [];
        (new Generator())
            ->getProcessorPipeline()
            ->walk(function ($processor) use (&$processors, &$defaultProcessors) {
                $rc = new \ReflectionClass($processor);
                $class = $rc->getName();

                $defaultProcessors[] = $class;
                $processors[] = [
                    'class' => $class,
                    'name' => $rc->getShortName(),
                    'default' => true,
                    'options' => $this->getOptionsDetails($rc),
                    'phpdoc' => $this->extractDocumentation($rc->getDocComment()),
                ];
            });

        $proccesorsDir = dirname((new \ReflectionClass(MergeIntoOpenApi::class))->getFileName());
        foreach (glob("$proccesorsDir/*.php") as $processor) {
            $class = 'OpenApi\\Processors\\' . pathinfo($processor, PATHINFO_FILENAME);
            if (!in_array($class, $defaultProcessors)) {
                $rc = new \ReflectionClass($class);

                if (!$rc->isInterface()) {
                    $processors[] = [
                        'class' => $rc->getName(),
                        'name' => $rc->getShortName(),
                        'default' => false,
                        'options' => $this->getOptionsDetails($rc),
                        'phpdoc' => $this->extractDocumentation($rc->getDocComment()),
                    ];
                }
            }
        }

        return $processors;
    }
}
