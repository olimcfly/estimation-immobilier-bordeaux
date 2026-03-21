<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\PageController;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ProcessusEstimationRouteTest extends TestCase
{
    public function testProcessusEstimationRouteIsRegistered(): void
    {
        $router = new Router();

        require base_path('routes/web.php');

        $reflection = new ReflectionMethod($router, 'resolveRoute');

        [$action, $params] = $reflection->invoke($router, 'GET', '/processus-estimation');

        $this->assertNotNull($action, 'The /processus-estimation route should be registered');
        $this->assertSame(PageController::class, $action[0]);
        $this->assertSame('processusEstimation', $action[1]);
        $this->assertSame([], $params);
    }

    public function testProcessusEstimationControllerMethodExists(): void
    {
        $this->assertTrue(
            method_exists(PageController::class, 'processusEstimation'),
            'PageController should have a processusEstimation method'
        );
    }

    public function testProcessusEstimationViewFileExists(): void
    {
        $viewFile = base_path('app/views/pages/processus_estimation.php');

        $this->assertFileExists($viewFile, 'The processus_estimation view file should exist');
    }

    public function testFooterContainsProcessusEstimationLink(): void
    {
        $footerFile = base_path('app/views/layouts/footer.php');
        $this->assertFileExists($footerFile);

        $footerContent = file_get_contents($footerFile);
        $this->assertIsString($footerContent);
        $this->assertStringContainsString(
            '/processus-estimation',
            $footerContent,
            'Footer should contain a link to /processus-estimation'
        );
    }
}
