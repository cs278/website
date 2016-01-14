<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @Route(service="app.cv_controller")
 */
final class CvController
{
    private $templating;
    private $jsonFile;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
        $this->jsonFile = __DIR__.'/../../../cv.json';
    }

    /**
     * @Route("/about/cv", name="cv")
     */
    public function indexAction(Request $request)
    {
        $cv = json_decode(file_get_contents($this->jsonFile), true);

        return new Response(
            $this->templating->render('about/cv.html.twig', $cv),
            Response::HTTP_OK
        );
    }
}
