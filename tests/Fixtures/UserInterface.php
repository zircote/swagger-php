<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Fixture for Context interface test", version="test")
 * @OA\Schema()
 */
interface UserInterface
{

    /**
     * The first name of the user.
     *
     * @return string
     * @example John
     * @OA\Property()
     */
    public function getFirstName();
}
