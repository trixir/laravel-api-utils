<?php

namespace Trixir\LaravelAPIUtils\Spec\OpenAPI;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Trixir\LaravelAPIUtils\Spec\OpenAPI\Resources\OpenAPIResourceGenerator;

/**
 * Class OpenAPIGenerator
 * @package Trixir\LaravelAPIUtils\Spec\OpenAPI
 */
class OpenAPIGenerator
{
    const SPEC_ROOT_FILE = 'openapi.php';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $spec = [];

    /**
     * @var OpenAPIResourceGenerator
     */
    private $resourceGenerator;

    /**
     * OpenAPIGenerator constructor.
     * @param Filesystem $filesystem
     * @param OpenAPIResourceGenerator $resourceGenerator
     */
    public function __construct(Filesystem $filesystem, OpenAPIResourceGenerator $resourceGenerator)
    {
        $this->filesystem = $filesystem;
        $this->resourceGenerator = $resourceGenerator;
    }

    public function initialize(string $rootFile)
    {

        if ($this->filesystem->exists($rootFile)) {
            $this->spec = $this->filesystem->getRequire($rootFile);
        } else {
            Log::warning("Trying to load a non-existent openAPI Spec file: " . $rootFile);
        }
    }

    /**
     * @param string $specPath
     * @return array
     * @throws FileNotFoundException
     */
    public function build(string $specPath)
    {
        if ($this->filesystem->isDirectory($specPath)) {
            foreach ($this->filesystem->directories($specPath) as $directory) {
                if (basename($directory) == 'paths') {
                    $this->walkSpecPathsDirectories($directory, $this->spec['paths']);
                } else {
                    $this->walkSpecDirectories($directory, $this->spec);
                }
            }
        }

        return $this->spec;
    }

    /**
     * @param string $path
     * @param string $name
     * @param array $validationRules
     * @return void
     */
    public function defineAPIResource(string $path, string $name, array $validationRules): void
    {
        $this->resourceGenerator->generateResource($this->spec, $path, $name, $validationRules);
    }

    /**
     * @param string $directory
     * @param array $specLevel
     * @throws FileNotFoundException
     */
    private function walkSpecDirectories(string $directory, array &$specLevel)
    {
        $name = basename($directory);
        if (!isset($specLevel[$name])) {
            $specLevel[$name] = [];
        }

        foreach ($this->filesystem->directories($directory) as $dir) {
            $this->walkSpecDirectories($dir, $specLevel[$name]);
        }

        foreach ($this->filesystem->files($directory) as $file) {
            $specLevel[$name][$file->getBasename('.php')] = $this->filesystem->getRequire($file->getRealPath());
        }
    }

    /**
     * @param string $directory
     * @param array $specLevel
     * @param string $fullPath
     * @throws FileNotFoundException
     */
    private function walkSpecPathsDirectories(string $directory, array &$specLevel, string &$fullPath = '')
    {
        $name = basename($directory);

        foreach ($this->filesystem->files($directory) as $file) {
            $baseName = $file->getBasename('.php');

            if (!isset($specLevel[Str::finish($fullPath, '/') . $name][$baseName])) {
                $specLevel[Str::finish($fullPath,
                    '/') . $name][$baseName] = $this->filesystem->getRequire($file->getRealPath());
            }
        }

        foreach ($this->filesystem->directories($directory) as $dir) {
            if ($name != 'paths') {
                $fullPath .= Str::start($name, '/');
            } else {
                $fullPath = '';
            }

            $this->walkSpecPathsDirectories($dir, $specLevel, $fullPath);
        }
    }
}
