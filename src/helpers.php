<?php declare(strict_types=1);

if (!function_exists('is_handling_http')) {
    function is_handling_http(): bool
    {
        return (bool)getenv('RR_HTTP') === true || (php_sapi_name() !== 'cli' && php_sapi_name() !== 'phpdbg');
    }
}
