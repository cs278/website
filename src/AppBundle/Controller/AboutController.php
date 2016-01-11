<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @Route(service="app.about_controller")
 */
final class AboutController
{
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @Route("/about", name="about")
     */
    public function indexAction(Request $request)
    {
        return new Response(
            $this->templating->render('about/index.html.twig'),
            Response::HTTP_OK
        );
    }
}
