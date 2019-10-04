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

use InvalidArgumentException;

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
     * @throws InvalidArgumentException
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

        throw new InvalidArgumentException("File [{$name}] not found.");
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
    public function getfounds()
    {
        return $this->founds;
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
}
