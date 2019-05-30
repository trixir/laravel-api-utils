<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:51 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

/**
 * Class BasePropertyModifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
abstract class BasePropertyModifier implements PropertyModifierInterface
{
    /**
     * @var array
     */
    protected $allRules;

    public function __construct(array $allRules)
    {
        $this->allRules = $allRules;
    }

    /**
     * @param string $rule
     * @return array
     */
    public function getDefinition(string $rule): array
    {
        return [
            "type" => $rule,
        ];
    }
}
