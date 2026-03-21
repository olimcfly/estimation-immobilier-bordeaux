<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ToolController
{
    public function calculatrice(): void
    {
        View::render('tools/calculatrice', [
            'page_title' => 'Calculatrice Immobilière Aix-en-Provence - Estimation Rapide',
        ]);
    }
}
