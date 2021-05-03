<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Twig;

final class DefaultController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        return new Response($this->twig->render('default/index.html.twig'));
    }
}
