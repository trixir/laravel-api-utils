<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class MinMaxModifierBase
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
abstract class MinMaxModifierBase extends BasePropertyModifier implements PropertyModifierInterface
{
    public function getDefinition(string $rule): array
    {
        $arr = explode(":", $rule);

        return [
            $this->getKey($arr[0]) => intval($arr[1]),
        ];
    }

    /**
     * min/max validators in Laravel handle all Integer, String and Array.
     * But in OpenAPI Spec definitions those are different namings.
     * Use this method to determine which one of those you're handling currently.
     *
     * @param string $prefix
     * @return string
     */
    protected function getKey(string $prefix): string
    {
        if (in_array("integer", $this->allRules)) {
            return "{$prefix}imum";
        } elseif (in_array("array", $this->allRules)) {
            return "{$prefix}Items";
        } else {
            return "{$prefix}Length";
        }
    }
}
