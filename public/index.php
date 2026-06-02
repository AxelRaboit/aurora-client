<?php

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    return new \App\Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
