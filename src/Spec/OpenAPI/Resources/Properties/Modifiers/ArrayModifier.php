<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class ArrayModifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
class ArrayModifier extends BasePropertyModifier implements PropertyModifierInterface
{
    /**
     * @param string $rule
     * @return array
     */
    public function getDefinition(string $rule): array
    {
        $result = parent::getDefinition($rule);

        $result["items"] = [
            "type" => "object",
            "properties" => [],
            "required" => [],
        ];

        return $result;
    }
}
