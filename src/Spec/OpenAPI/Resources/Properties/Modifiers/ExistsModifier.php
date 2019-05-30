<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 4:52 PM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers;

use Illuminate\Support\Str;

/**
 * Class ExistsModifier
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers
 */
class ExistsModifier extends BasePropertyModifier implements PropertyModifierInterface
{
    public function getDefinition(string $rule): array
    {
        $def = explode(":", $rule)[1];
        $arr = explode(",", $def);

        $model = Str::singular(Str::studly($arr[0]));
        $attribute = $arr[1] ?? "ID";

        return [
            'description' => "The '{$attribute}' attribute from an existing $model resource in that Game's context.",
            "required" => true,
        ];
    }
}
