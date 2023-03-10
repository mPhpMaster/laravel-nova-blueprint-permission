<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

//if ( !function_exists('') ) {
//    /***/
//    function ()
//    {
//        return;
//    }
//}

if( !\defined('NAMESPACE_SEPARATOR') ) {
    \define('NAMESPACE_SEPARATOR', '\\');
}

if( !function_exists('grep') ) {
    /**
     * @param $data
     * @param $grep
     *
     * @return array
     */
    function grep($data, $grep)
    {
        $is = is_array($data) ? $data : [ $data ];

        return mapEach($is, function($value) use ($grep) {
            if( stringContains($value, $grep) ) {
                return $value;
            }

            return null;
        });

    }
}

if( !function_exists('getInterfaces') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getInterfaces($grep = null)
    {
        $result = array_values(get_declared_interfaces());
        if( !is_null($grep) ) {
            $result = filterEach($result, $grep, false);
        }

        return $result;
    }
}

if( !function_exists('getClasses') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getClasses($grep = null)
    {
        $result = array_values(get_declared_classes());
        if( !is_null($grep) ) {
            $result = filterEach($result, $grep, false);
        }

        return $result;
    }
}

if( !function_exists('getTraits') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getTraits($grep = null)
    {
        $result = array_values(get_declared_traits());
        if( !is_null($grep) ) {
            $result = filterEach($result, $grep, false);
        }

        return $result;
    }
}

if( !function_exists('getAllDeclared') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getAllDeclared($grep = null)
    {
        $result = array_merge(getClasses(), getInterfaces(), getTraits());
        if( !is_null($grep) ) {
            $result = filterEach($result, $grep, false);
        }

        return array_values($result);
    }
}

if( !function_exists('getModelAbstractClass') ) {
    /**
     * @param object|string|null $test_class
     *
     * @return string|bool
     * @todo: change the return to model parent class
     */
    function getModelAbstractClass($test_class = null)
    {
        if( $test_class ) {
            $test_class = is_object($test_class) ? $test_class : app(getRealClassName($test_class));

            $test_abstract_class = getModelAbstractClass();

            return $test_class instanceof $test_abstract_class;
        }

        return (new ReflectionClass('Model'))->name;
    }
}

// region: data loop
if( !function_exists('dataForEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     * @param bool        $map
     *
     * @return array
     */
    function dataForEach($array, callable $callback, $map = true)
    {
        $result = [];

        foreach( (array) $array as $key => $value ) {
            $crnt_data = [ 'value' => &$value, 'key' => &$key, 'all' => &$array/*, 'result' => &$result*/ ];

            try {
                $return = call_user_func_array($callback, [ &$value, &$key, &$array, &$crnt_data ]);

                if( $map ) {
                    if( isClosure($map) ) {
                        call_user_func_array($map, [
                            &$return,
                            $put = function($newValue = null, $newKey = null) use (&$result, &$return, &$key, &$value) {
                                if( func_num_args() === 0 ) {
                                    $result[ $key ] = $value;
                                } elseif( func_num_args() === 1 ) {
                                    $result[ $key ] = $newValue;
                                } elseif( func_num_args() === 2 ) {
                                    $result[ $newKey ] = $newValue;
                                }

                                return $result;
                            },
                            $skip = function() use (&$result) {
                                return $result;
                            },
                            &$crnt_data,
                        ]);
                    } elseif( !is_null($return) ) {
                        $result[ $key ] = $return;
                    }
                }
            } catch(Exception $exception) {
                break;
            }
        }

        return $map ? $result : $array;
    }
}

if( !function_exists('applyEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     *
     * @return array
     */
    function applyEach($array, callable $callback)
    {
        return dataForEach($array, $callback, false);
    }
}

if( !function_exists('mapEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     *
     * @return array
     */
    function mapEach($array, callable $callback)
    {
        return dataForEach($array, $callback, true);
    }
}

if( !function_exists('filterEach') ) {
    /**
     * @param array|mixed   $array
     * @param callable|null $for
     * @param bool          $strict
     *
     * @return array
     */
    function filterEach($array, $for = null, $strict = false)
    {
        $callback = function($v) use ($for, $strict, $array) {
            $v = is_bool($v) && $v ? "true" : "false";

            return stringContains($v, $for) !== false ||
                (
                    $strict === false &&
                    stringContains(snake_case($v), mapEach($for, fromCallable('snake_case'))) !== false
                );
        };

        $map = function($returns, $put, $skip, $data) use ($strict) {
            $pass = $strict ? $returns !== false : ! !$returns;
            ($pass && $put($data[ 'value' ])) || $skip;
        };

        return dataForEach($array, $callback, $map);
    }
}
// endregion: data loop

if( !function_exists('getNewValidator') ) {
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return \Illuminate\Validation\Validator
     */
    function getNewValidator($data, $rules, $messages = [], $customAttributes = [])
    {
        return Illuminate\Support\Facades\Validator::make($data, $rules, $messages = [], $customAttributes = []);
    }
}

if( !function_exists('createNewValidator') ) {
    /**
     * Create New Validator.
     *
     * @param string        $name
     * @param \Closure|null $closure
     */
    function createNewValidator($name, Closure $closure = null)
    {
        \Illuminate\Support\Facades\Validator::extend(trim($name), $closure && $closure instanceof Closure ? $closure : fn($attribute, $value, $parameters) => $value);
    }
}

if( !function_exists('macros') ) {
    /**
     * Require php file that returns an array contains classFQN => methodName => closure
     *
     * @param string|\Closure|array $path
     */
    function macros($path)
    {
        $data = null;

        if( $path instanceof Closure ) {
            $data = call_user_func($path);
            if( is_string($data) ) {
                $path = $data;
                $data = null;
            }
        }

        if( is_string($path) ) {
            if( !file_exists($path) ) {
                throw new Exception("The given path [{$path}] doesn't exist!");
            }
            $data = \Illuminate\Support\Facades\File::requireOnce($path);
        }

        if( is_array($path) ) {
            $data = $path;
        }

        if( !is_array($data) ) {
            throw new Exception("The given data must be array, " . gettype($data) . " given.");
        }

        foreach( $data as $class => $methods ) {
            if( !is_string($class) || !is_array($methods) || !method_exists($class, 'macro') ) {
                continue;
            }

            foreach( $methods as $name => $method ) {
                if( !is_string($name) || !is_callable($method) && !is_object($method) ) {
                    continue;
                }

                $class::macro($name, $method);
            }
        }
    }
}

if( !function_exists('fromCallable') ) {
    /**
     * @param string|\Closure|callable $callable
     *
     * @return \Closure|mixed
     * @throws \Exception
     */
    function fromCallable($callable)
    {
        if( $callable instanceof Closure ) {
            return $callable;
        }

        if( is_string($callable) ) {
            if( !is_callable($callable) ) {
                throw new Exception("The given name [{$callable}] is not callable!");
            }

            return \Closure::fromCallable($callable);
        }

        return $callable;
    }
}
