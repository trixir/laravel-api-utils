<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 11:36 AM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties;

/**
 * Class OpenAPIProperties
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties
 */
class OpenAPIProperties
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $requiredProperties = [];

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getRequiredProperties(): array
    {
        return $this->requiredProperties;
    }

    /**
     * @param string $name
     * @param array $definition
     * @return void
     */
    public function addProperty(string $name, array $definition): void
    {
        $this->properties[$name] = $definition;

        $nested = $this->checkForNestedProperties($name, $definition);
        if (false === $nested) {
            $whereToAdd = &$this->requiredProperties;
            $whatToUnset = &$this->properties[$name];
        } else {
            $name = $nested[1];
            $whereToAdd = &$this->properties[$nested[0]]["items"]["required"];
            $whatToUnset = &$this->properties[$nested[0]]["items"]['properties'][$name];
        }

        if (isset($definition['required']) && $definition['required']) {
            $whereToAdd[] = $name;
            unset($whatToUnset['required']);
        }
    }

    /**
     * If the property is nested it will return the Parent's name and the Sub-Property name in an array.
     * e.g. "media.*.filename" will result in => ['media', 'filename']
     *
     * False otherwise.
     *
     * @param string $name
     * @param array $definition
     * @return array|false
     */
    private function checkForNestedProperties(string $name, array &$definition)
    {
        if (strpos($name, '.*.') !== false) {
            $arr = explode('.*.', $name);

            $this->properties[$arr[0]]["items"]["properties"][$arr[1]] = $definition;
            unset($this->properties[$name]);

            return $arr;
        } else {
            return false;
        }
    }
}
