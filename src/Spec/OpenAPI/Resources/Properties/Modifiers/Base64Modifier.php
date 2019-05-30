<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class Base64Modifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
class Base64Modifier extends BasePropertyModifier implements PropertyModifierInterface
{
    public function getDefinition(string $rule): array
    {
        return [
            "type" => "string",
            "format" => "base64",
        ];
    }
}
