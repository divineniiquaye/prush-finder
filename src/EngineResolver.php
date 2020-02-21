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

class EngineResolver
{
    /**
     * The array of engine resolvers.
     *
     * @var array
     */
    protected $resolvers = [];

    /**
     * The resolved engine instances.
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * Register a new engine resolver.
     *
     * The engine string typically corresponds to a file extension.
     *
     * @param  string   $engine
     * @param  \Closure  $resolver
     * @return void
     */
    public function register($engine, $resolver)
    {
        unset($this->resolved[$engine]);

        if ($resolver instanceof \Closure) {
            $this->resolvers[$engine] = $resolver;
        } else {
            $this->resolvers[$engine] = function () use ($resolver) {
                if (is_object($resolver)) {
                    return $resolver;
                }

                return new $resolver();
            };
        }
    }

    /**
     * Resolve an engine instance by name.
     *
     * @param  string  $engine
     * @return Interfaces\EngineInterface
     *
     * @throws Exceptions\PrushException
     */
    public function resolve($engine)
    {
        if (isset($this->resolved[$engine])) {
            return $this->resolved[$engine];
        }

        if (isset($this->resolvers[$engine])) {
            return $this->resolved[$engine] = $this->invokeResolved($this->resolvers[$engine]);
        }

        throw new Exceptions\PrushException("Engine [{$engine}] is not available.");
    }

    /**
     * Invokes as callback of Closure.
     *
     * This implements `call_user_func`.
     *
     * @param mixed $function
     * @param array $arguements
     *
     * @return mixed Function results
     */
    private function invokeResolved($resolved, $arguements = [])
    {
        return $resolved(...$arguements);
    }
}
