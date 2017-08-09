<?php
/**
 * @license Apache 2.0
 */
namespace Swagger\Processors;

use Swagger\Annotations\Operation;
use Swagger\Annotations\Path;
use Swagger\Analysis;

/**
 * Handle the format properties from parent classes;
 */
class HandleFormat
{

    public function __invoke(Analysis $analysis)
    {
        if (!is_null($analysis->swagger->paths)) {
            /** @var Path $path */
            foreach ($analysis->swagger->paths as $path) {
                foreach ($path as $key => $value) {
                    if ($value instanceof Operation && !is_null($value->responses)) {
                        if (!is_null($value->parameters)) {
                            foreach ($value->parameters as $item) {
                                if ($item->format == 'json'){
                                    $item->default = json_encode($item->default);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
