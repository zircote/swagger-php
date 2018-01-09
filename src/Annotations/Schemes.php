<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Swagger\Annotations;


/**
 * Class Schemes
 *
 * @package Swagger\Annotations
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @Annotation
 */
class Schemes extends AbstractAnnotation
{
    /**
     * Http schemes
     *
     * @var array
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $schemes;

    /**
     * @inheritdoc
     */
    public static $_blacklist = ['_context', '_unmerged'];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        'Swagger\Annotations\OpenApi'
    ];
}
