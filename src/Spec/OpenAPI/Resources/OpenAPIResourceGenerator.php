<?php
/**
 * Created by PhpStorm.
 * User: imadzharov
 * Date: 5/16/19
 * Time: 10:51 AM
 */

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources;

use Illuminate\Support\Str;
use Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\OpenAPIProperties;
use Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\Properties\OpenAPIValidationMapper;

/**
 * Class OpenAPIResourceGenerator
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources
 */
class OpenAPIResourceGenerator
{
    /**\
     * @var OpenAPIValidationMapper
     */
    private $validationMapper;

    /**
     * OpenAPIResourceGenerator constructor.
     * @param OpenAPIValidationMapper $validationMapper
     */
    public function __construct(OpenAPIValidationMapper $validationMapper)
    {
        $this->validationMapper = $validationMapper;
    }

    /**
     * @param array $spec
     * @param string $path
     * @param string $name
     * @param array $validationRules
     * @return void
     */
    public function generateResource(array &$spec, string $path, string $name, array $validationRules): void
    {
        $properties = $this->validationMapper->mapRulesToProperties($validationRules);

        foreach ($this->createSchemas($name, $properties) as $schemaKey => $definition) {
            $spec["components"]['schemas'][$schemaKey] = $definition;
        }

        foreach ($this->createParameters($name) as $paramKey => $definition) {
            $spec["components"]['parameters'][$paramKey] = $definition;
        }

        foreach ($this->createPaths($path, $name) as $pathKey => $definition) {
            $spec['paths'][$pathKey] = $definition;
        }
    }

    /**
     * @param string $name
     * @param OpenAPIProperties $properties
     * @return array
     */
    private function createSchemas(string $name, OpenAPIProperties $properties): array
    {
        return [
            "{$name}Properties" => $this->getSchemaProperties($properties->getProperties()),
            "{$name}Model" => $this->getSchemaModel($name, $properties->getRequiredProperties()),
            "{$name}" => $this->getSchemaObject($name),
            "{$name}Resource" => $this->getSchemaResource($name),
            "{$name}Collection" => $this->getSchemaCollection($name),
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    private function createParameters(string $name): array
    {
        $camelName = Str::camel($name);

        return [
            $camelName => [
                "name" => $camelName,
                "in" => "path",
                "description" => "ID of a $name resource",
                "required" => true,
                "schema" => [
                    "type" => "integer",
                    "minimum" => 1,
                ],
            ]
        ];
    }

    /**
     * @param string $path
     * @param string $name
     * @return array
     */
    private function createPaths(string $path, string $name): array
    {
        $path = Str::finish($path, '/');
        $camel = Str::camel($name);
        $kebab = Str::kebab($name);

        $tags = [$name];
        $idParam = ['$ref' => "#/components/parameters/$camel"];

        return [
            "{$path}{$kebab}" => [
                "get" => $this->getPathGetAll($tags, $name),
                "post" => $this->getPathPost($tags, $name),
            ],
            "{$path}{$kebab}/{" . $camel . "}" => [
                "get" => $this->getPathGet($tags, $name, $idParam),
                "put" => $this->getPathPut($tags, $name, $idParam),
                "patch" => $this->getPathPatch($tags, $name, $idParam),
                "delete" => $this->getPathDelete($tags, $name, $idParam),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Schema help methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * @param array $properties
     * @return array
     */
    private function getSchemaProperties(array $properties): array
    {
        return [
            "type" => "object",
            "properties" => $properties,
        ];
    }

    /**
     * @param string $name
     * @param array $required
     * @return array
     */
    private function getSchemaModel(string $name, array $required): array
    {
        $result = [
            "allOf" => [
                [
                    '$ref' => "#/components/schemas/{$name}Properties",
                ],
            ],
        ];

        if (!empty($required)) {
            $result["allOf"][] =
                [
                    "type" => "object",
                    "required" => $required,
                ];
        }

        return $result;
    }

    /**
     * @param string $name
     * @return array
     */
    private function getSchemaObject(string $name): array
    {
        return [
            "allOf" => [
                [
                    '$ref' => "#/components/schemas/GameModel",
                ],
                [
                    '$ref' => "#/components/schemas/{$name}Model",
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    private function getSchemaResource(string $name): array
    {
        return [
            "type" => "object",
            "properties" => [
                "data" => [
                    '$ref' => "#/components/schemas/{$name}",
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    private function getSchemaCollection(string $name): array
    {
        return [
            "allOf" => [
                [
                    '$ref' => "#/components/schemas/ResourceCollection",
                ],
                [
                    "type" => "object",
                    "required" => [
                        "data",
                    ],
                    "properties" => [
                        "data" => [
                            "type" => "array",
                            "items" => [
                                '$ref' => "#/components/schemas/$name",
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Common responses help methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * @param array $only
     * @return array
     */
    private function getResponses(array $only = []): array
    {
        $responses = [
            "204" => [
                '$ref' => "#/components/responses/Empty",
            ],
            "400" => [
                '$ref' => "#/components/responses/BadRequest",
            ],
            "401" => [
                '$ref' => "#/components/responses/Unauthenticated",
            ],
            "403" => [
                '$ref' => "#/components/responses/Unauthorized",
            ],
            "404" => [
                '$ref' => "#/components/responses/NotFound",
            ],
        ];

        return empty($only) ? $responses : array_intersect_key($responses, array_flip($only));
    }


    /*
    |--------------------------------------------------------------------------
    | Common parameters help methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * @return array
     */
    private function getParams(): array
    {
        return [
            [
                '$ref' => "#/components/parameters/game",
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Path help methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * @param array $tags
     * @param string $name
     * @return array
     */
    private function getPathGetAll(array $tags, string $name)
    {
        return [
            "tags" => $tags,
            "summary" => "Retrieves a collection of $name resources",
            "parameters" => $this->getParams(),
            "responses" => [
                "200" => [
                    "description" => "A collection of $name resources",
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                '$ref' => "#/components/schemas/{$name}Collection",
                            ],
                        ],
                    ],
                ],
            ] + $this->getResponses([401, 403]),
        ];
    }

    /**
     * @param array $tags
     * @param string $name
     * @return array
     */
    private function getPathPost(array $tags, string $name): array
    {
        return [
            "tags" => $tags,
            "summary" => "Creates a new $name resource",
            "parameters" => $this->getParams(),
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            '$ref' => "#/components/schemas/{$name}Model",
                        ],
                    ],
                ],
            ],
            "responses" => [
                "201" => [
                    "description" => "The newly created $name resource",
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                '$ref' => "#/components/schemas/{$name}Resource",
                            ],
                        ],
                    ],
                ],
            ] + $this->getResponses([400, 401, 403]),
        ];
    }

    /**
     * @param array $tags
     * @param string $name
     * @param array $idParam
     * @return array
     */
    private function getPathGet(array $tags, string $name, array $idParam): array
    {
        return [
            "tags" => $tags,
            "summary" => "Retrieves a $name resource",
            "parameters" => array_merge($this->getParams(), [$idParam]),
            "responses" => [
                "200" => [
                    "description" => "A $name resource",
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                '$ref' => "#/components/schemas/{$name}Resource",
                            ],
                        ],
                    ],
                ],
            ] + $this->getResponses([401, 403, 404]),
        ];
    }

    /**
     * @param array $tags
     * @param string $name
     * @param array $idParam
     * @return array
     */
    private function getPathPut(array $tags, string $name, array $idParam): array
    {
        return [
            "tags" => $tags,
            "summary" => "Replaces a $name resource",
            "parameters" => array_merge($this->getParams(), [$idParam]),
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            '$ref' => "#/components/schemas/{$name}Model",
                        ],
                    ],
                ],
            ],
            "responses" => $this->getResponses(),
        ];
    }

    /**
     * @param array $tags
     * @param string $name
     * @param array $idParam
     * @return array
     */
    private function getPathPatch(array $tags, string $name, array $idParam): array
    {
        return [
            "tags" => $tags,
            "summary" => "Updates attribute(s) of a given $name resource",
            "parameters" => array_merge($this->getParams(), [$idParam]),
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            '$ref' => "#/components/schemas/{$name}Properties",
                        ],
                    ],
                ],
            ],
            "responses" => $this->getResponses(),
        ];
    }

    /**
     * @param array $tags
     * @param string $name
     * @param array $idParam
     * @return array
     */
    private function getPathDelete(array $tags, string $name, array $idParam): array
    {
        return [
            "tags" => $tags,
            "summary" => "Deletes a $name resource",
            "parameters" => array_merge($this->getParams(), [$idParam]),
            "responses" => $this->getResponses([204, 401, 403, 404]),
        ];
    }
}
