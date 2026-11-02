<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\ComponentNamer;

use OpenApi\OpenApiException;

/**
 * Names a component after its class's fully qualified name, so classes that share a short name
 * do not collide.
 *
 * The namespace separators become `$separator`, after `$strip` is removed from the front:
 * `App\Dto\User\CreateDTO` with `strip: 'App\Dto\'` is `User.CreateDTO`, and `UserCreateDTO`
 * with an empty separator. A class outside `$strip` keeps its whole name.
 *
 * A member of a `#[OA\Components]` holder is still named after the member.
 */
class QualifiedName extends ShortName
{
    /**
     * The characters OpenAPI allows in a component name, besides letters and digits.
     */
    protected const SEPARATORS = ['', '.', '-', '_'];

    protected string $strip;

    public function __construct(
        string $strip = '',
        protected string $separator = '.',
    ) {
        if (!in_array($separator, static::SEPARATORS, true)) {
            throw new OpenApiException(sprintf('Invalid separator "%s": a component name allows only "%s"', $separator, implode('", "', static::SEPARATORS)));
        }

        $this->strip = trim($strip, '\\') === '' ? '' : trim($strip, '\\') . '\\';
    }

    protected function className(\ReflectionClass $class): string
    {
        $name = $class->getName();

        if ($this->strip !== '' && str_starts_with($name, $this->strip)) {
            $name = substr($name, strlen($this->strip));
        }

        return str_replace('\\', $this->separator, $name);
    }
}
