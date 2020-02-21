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

namespace Prush\Interfaces;

interface PrushResolverInterface
{
    /**
     * Get the fully qualified location of the file.
     *
     * @param  string  $file
     *
     * @return string
     */
    public function find($file);

    /**
     * Add a location to the finder.
     *
     * @param  string  $location
     *
     * @return void
     */
    public function addLocation($location);

    /**
     * Prepend a location to the finder.
     *
     * @param string $location
     */
    public function prependLocation($location);

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints);

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints);

    /**
     * Add a valid view extension to the finder.
     *
     * @param  string  $extension
     *
     * @return void
     */
    public function addExtension($extension);

    /**
     * Returns whether or not the view name has any hint information.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasHintInformation($name);

    /**
     * Flush the cache of located views.
     *
     * @return void
     */
    public function flush();
}
