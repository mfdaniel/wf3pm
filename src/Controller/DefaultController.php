<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DefaultController
{
    public function homepage(Environment $twig){
        $html = 'Hello World! ';
        return new Response($twig->render('Default/homepage.html.twig'));
    }
}

