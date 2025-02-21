<?php

if( !defined('DEBUG_METHODS') ) {
    define('DEBUG_METHODS', [
        'getDebugBacktrace',
        'collectTrace',
        'traceInfo',
        'dumpDebug',
        'getDumpOutput',
        'dump',
        'du',
        'dx',
        'd',
        'dd',
        'duE',
        'dE',
    ]);
}

if( !function_exists('isDebugEnabled') ) {
    /**
     * @return bool
     */
    function isDebugEnabled(): bool
    {
        return config('app.debug');
    }
}

if( !function_exists('cutBasePath') ) {
    /**
     * Remove base_path() from the given file path.
     *
     * @param string|null $_fullFilePath file path
     * @param string      $prefix        any text to prefix the result with.
     *
     * @return string
     */
    function cutBasePath(string $_fullFilePath = null, string $prefix = ''): string
    {
        $fullFilePath = $_fullFilePath ?:
            array_get(
                head(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)),
                'file'
            );

        return $prefix . str_ireplace(
                fixPath(base_path() . DIRECTORY_SEPARATOR),
                '',
                fixPath($fullFilePath ?: __FILE__)
            );
    }
}

#region DEBUG
if( !function_exists('collectTrace') ) {
    /**
     * @param null $file
     * @param null $line
     * @param null $object
     * @param null $method
     * @param null $string
     * @param null $debugTrace
     *
     * @return array
     */
    function collectTrace(
        &$file = null,
        &$line = null,

        &$object = null,
        &$method = null,

        &$string = null,
        &$debugTrace = null
    ): array {
        try {
            $call = [];
            if( !isDebugEnabled() ) {
                {
                    return compact('line', 'file', 'call', 'string', 'debugTrace');
                }
            }

            $debugTrace = $debugTrace ?: @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            if( !empty($debugTrace) and is_array($debugTrace) ) {
                $idx = -1;
                $searchin = array_flip(DEBUG_METHODS);
                foreach( $debugTrace as $key => $item ) {
                    $functionName = array_get($item, 'function', false);
                    if( $functionName && Arr::has(
                            $searchin,
                            $functionName
                        ) ) {
                        $idx = $key;
                    }
                }
//                toCollect(data_get($debugTrace, '*.function'))->take(count(DEBUG_METHODS) + 2)->map->function->search(function ($fn) use(&$idxs){
//                    return \Illuminate\Support\Arr::has(DEBUG_METHODS, $fn);
//                });
                @reset($debugTrace);
                if( (int) $idx > -1 ) {
                    try {
                        $calls = array_splice($debugTrace, (int) $idx, 2);
                        $call = array_merge(
                            array_get($calls, '1'),
                            array_only(array_get($calls, '0'), [ 'file', 'line' ])
                        );
                    } /** @noinspection PhpUnusedLocalVariableInspection */ catch(Exception $exception) {
                        $call = @current($debugTrace);
                    }

//                    for ($counter = 0; $counter < $idx; $counter++)
//                        $call = @next($debugTrace);
                } else {
                    $call = @current($debugTrace);
                }
            } else {
                $debugTrace = is_array($debugTrace) ? print_r($debugTrace, true) : $debugTrace;
                throw new Exception(__LINE__ . " function debug_backtrace() returned: $debugTrace");
            }

            $line = ($call[ 'line' ] ?? __LINE__);
            $file = @basename(($call[ 'file' ] ?? cutBasePath()));
            $method = ($call[ 'function' ] ?? __METHOD__);
            $object = ($call[ 'class' ] ?? __CLASS__);
            $parentRelatoinType = ($call[ 'type' ] ?? '::');

            $classWithMethod = iif($object, isConsole((string) ($object), "<b style='color: #6c1512'>$object</b>")) .
                iif($object && $method, getAny($parentRelatoinType, '::'), '') .
                iif($method, isConsole((string) ($method), "<b style='color: #0b6c0e'>$method</b>"));

            $fileWithLine = $file .
                iif($file && $line, isConsole(':', '<b>:</b>'), '') .
                iif(
                    $line,
                    isConsole((string) ($line), "<b>$line</b>")
                );
            $string =
                iif(
                    $classWithMethod,
                    isConsole((string) ($classWithMethod), "<b style='color: #0e566c'>$classWithMethod</b>")
                ) .
                iif($fileWithLine, isConsole("    | $fileWithLine", "    <b>|</b><small style='color: blue;'>$fileWithLine</small>"));

        } catch(Exception $e) {
            dd(
                __FILE__ . ':' . __LINE__,
                $e->getMessage(),
                collect(debug_backtrace())
            );
        }

        return compact(
            'line',
            'file',
            'call',
            'string',
            'debugTrace'
        );
    }
}

if( !function_exists('traceInfo') ) {
    /**
     * @param null $debugBacktrace
     *
     * @return array
     */
    function traceInfo(&$debugBacktrace = null): array
    {
        $file = null;
        $line = null;
        $method = null;
        $debugBacktrace = $debug_backtrace ?? @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $string = null;

        return collectTrace($file, $line, $class, $method, $string, $debugBacktrace);
    }
}
