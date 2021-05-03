<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Twig;

final class AboutController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/about", name="about")
     */
    public function indexAction(Request $request)
    {
        return new Response(
            $this->twig->render('about/index.html.twig'),
            Response::HTTP_OK
        );
    }
}
