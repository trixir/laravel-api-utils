<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class RequiredModifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
class RequiredModifier extends BasePropertyModifier implements PropertyModifierInterface
{
    /**
     * @param string $rule
     * @return array
     */
    public function getDefinition(string $rule): array
    {
        if (in_array('sometimes', $this->allRules) || in_array('nullable', $this->allRules)) {
            return [];
        } else {
            return [
                "required" => true,
            ];
        }
    }
}
