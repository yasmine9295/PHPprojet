<?php
namespace App\Controllers;

use App\Core\Render;

class Admin
{

    public function users(): void
    {
        $render = new Render("users", "backoffice");
        $render->render();
    }
}
