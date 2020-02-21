<?php

/*
 * This code is under BSD 3-Clause "New" or "Revised" License.
 *
 * PHP version 7 and above required
 *
 * @category  Prush
 *
 * @author    Divine Niiquaye Ibok <divineibok@gmail.com>
 * @copyright 2019 Biurad Group (https://biurad.com/)
 * @license   https://opensource.org/licenses/BSD-3-Clause License
 *
 * @link      https://www.biurad.com/projects/prush
 * @since     Version 0.1
 */

namespace Prush;

use BiuradPHP\Template\Interfaces\ViewInterface;

class PrushResolver implements Interfaces\PrushResolverInterface
{
    /**
     * The array of active view paths.
     *
     * @var array
     */
    protected $paths;

    /**
     * The array of files that have been located.
     *
     * @var array
     */
    protected $founds = [];

    /**
     * Register a view extension with the finder.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * The namespace to file path hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * Create a new file view loader instance.
     *
     * @param array      $paths
     * @param array|null $extensions
     */
    public function __construct(array $paths, array $extensions = null)
    {
        $this->paths = array_map([$this, 'resolvePath'], $paths);

        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function find($file)
    {
        if (isset($this->founds[$file])) {
            return $this->founds[$file];
        }

        if ($this->hasHintInformation($name = trim($file))) {
            return $this->founds[$name] = $this->findNamespacedView($name);
        }

        return $this->founds[$file] = $this->findInPaths($file, $this->paths);
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param array  $paths
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as $path) {
            foreach ($this->getPossibleFiles($name) as $file) {
                if (file_exists($Path = $path.'/'.$file)) {
                    return $Path;
                }
            }
        }

        throw new \InvalidArgumentException("File [{$name}] not found.");
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        [$namespace, $view] = $this->parseNamespaceSegments($name);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Get the segments of a template with a named path.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseNamespaceSegments($name)
    {
        $segments = explode(ViewInterface::HINT_PATH_DELIMITER, $name);
        $segments[0] = str_replace(['@', '#'], '', $segments[0]);

        if (count($segments) !== 2) {
            throw new \InvalidArgumentException("View [{$name}] has an invalid name.");
        }

        if (! isset($this->hints[$segments[0]])) {
            throw new \InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Get an array of possible view files.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getPossibleFiles($name)
    {
        return array_map(function ($extension) use ($name) {
            return str_replace('.', '/', $name).'.'.$extension;
        }, $this->extensions);
    }

    /**
     * {@inheritDoc}
     */
    public function addLocation($location)
    {
        $this->paths[] = $this->resolvePath($location);
    }

    /**
     * {@inheritDoc}
     */
    public function prependLocation($location)
    {
        return array_unshift($this->paths, $this->resolvePath($location));
    }

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Resolve the path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolvePath($path)
    {
        return realpath($path) ?: $path;
    }

    /**
     * Register an extension with the files finder.
     *
     * @param string $extension
     */
    public function addExtension($extension)
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Flush the cache of located files.
     */
    public function flush()
    {
        $this->founds = [];
    }

    /**
     * Set the active file paths.
     *
     * @param array $paths
     *
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Get the active file paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Get the founds that have been located.
     *
     * @return array
     */
    public function getFounds()
    {
        return $this->founds;
    }

    /**
     * Get the namespace to file path hints.
     *
     * @return array
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Get registered extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasHintInformation($name)
    {
        return strpos($name, ViewInterface::HINT_PATH_DELIMITER) > 0;
    }
}
