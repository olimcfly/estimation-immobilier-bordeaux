<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class PageController
{
    public function services(): void
    {
        View::render('pages/services');
    }

    public function aPropos(): void
    {
        View::render('pages/a_propos');
    }

    public function contact(): void
    {
        View::render('pages/contact');
    }
}
