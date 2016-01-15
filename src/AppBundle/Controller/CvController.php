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

        // Convert dates into DateTime objects.
        array_walk_recursive($cv, function (&$value, $key) {
            if ($key === 'startDate' || $key === 'endDate') {
                $value = \DateTime::createFromFormat('Y-m-d', $value);
            }
        });

        if (true) {
            // Apply sanitisation.
            unset($cv['basics']['phone']);
            unset($cv['basics']['location']['address']);
            unset($cv['basics']['location']['postalCode']);

            $cv['references'] = [];
        }

        $profiles = [];

        foreach ($cv['basics']['profiles'] as $profile) {
            $network = mb_strtolower($profile['network']);
            // Remove non letters/numbers Unicode aware of course.
            $network = preg_replace('{[^\p{L}\p{N}]}', '', $network);

            $profiles[$network] = [
                'username' => $profile['username'],
                'url' => $profile['url'],
            ];
        }

        $cv['profiles'] = $profiles;

        return new Response(
            $this->templating->render('about/cv.html.twig', $cv),
            Response::HTTP_OK
        );
    }
}
