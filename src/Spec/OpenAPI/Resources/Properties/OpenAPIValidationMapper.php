<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 11:35 AM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties;

use Log;
use Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\Modifiers\PropertyModifierInterface;

/**
 * Class OpenAPIValidationMapper
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties
 */
class OpenAPIValidationMapper
{
    /**
     * @param array $validationRules
     * @return OpenAPIProperties
     */
    public function mapRulesToProperties(array $validationRules): OpenAPIProperties
    {
        $result = new OpenAPIProperties();

        foreach ($validationRules as $name => $rules) {
            $definition = [];

            //TODO Handle the case when they are objects not only strings
            $allRules = explode('|', $rules);
            foreach ($allRules as $rule) {
                if (!empty($rule)) {
                    $this->modifyDefinition($definition, $rule, $allRules);
                }
            }

            $result->addProperty($name, $definition);
        }

        return $result;
    }

    /**
     * Will return True only if that $rule led to a modification and False otherwise.
     *
     * @param array $definition
     * @param string $rule
     * @param array $allRules
     * @return void
     */
    private function modifyDefinition(array &$definition, string $rule, array $allRules): void
    {
        $className = __NAMESPACE__ . "\\Modifiers\\" . $this->getRuleName($rule) . "Modifier";

        if (class_exists($className)) {

            /** @var PropertyModifierInterface $modifier */
            $modifier = new $className($allRules);
            $definition = array_merge($definition, $modifier->getDefinition($rule));
        } else {
            Log::info("OpenAPI Spec property modifier $className is not defined.");
        }
    }

    /**
     * @param string $rule
     * @return string
     */
    private function getRuleName(string $rule): string
    {
        return ucfirst(strpos($rule, ":") ? explode(':', $rule)[0] : $rule);
    }
}
