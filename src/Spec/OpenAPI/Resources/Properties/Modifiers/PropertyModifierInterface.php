<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:47 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Interface PropertyModifierInterface
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
interface PropertyModifierInterface
{
    /**
     * @param string $rule
     * @return array
     */
    public function getDefinition(string $rule): array;
}
