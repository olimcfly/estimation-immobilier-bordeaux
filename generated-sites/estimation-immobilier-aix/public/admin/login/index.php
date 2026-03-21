<?php

declare(strict_types=1);

// Fallback entry point for /admin/login when URL rewriting is unavailable.
// Forwards to the main front controller so the router handles the request.

require dirname(__DIR__, 2) . '/index.php';
