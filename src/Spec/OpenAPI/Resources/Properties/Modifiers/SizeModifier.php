<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class SizeModifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
class SizeModifier extends BasePropertyModifier implements PropertyModifierInterface
{
    /**
     * @param string $rule
     * @return array
     */
    public function getDefinition(string $rule): array
    {
        return [
            "exactSize" => intval(explode(":", $rule)[1]),
        ];
    }
}
