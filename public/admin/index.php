<?php

declare(strict_types=1);

// Redirect /admin/ to the main front controller so the router handles it.
// Front controller for /admin/ routes.
// Forwards all requests to the main index.php so the router handles them.

require dirname(__DIR__) . '/index.php';
