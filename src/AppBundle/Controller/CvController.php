<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Twig;

final class CvController
{
    private Twig $twig;
    private $jsonFile;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
        $this->jsonFile = __DIR__.'/../../../cv.json';
    }

    /**
     * @Route("/about/cv", name="cv")
     */
    public function indexAction(Request $request)
    {
        $response = new Response();
        $response->setLastModified(new \DateTimeImmutable('@'.\filemtime($this->jsonFile)));
        $response->setEtag(\hash_file('sha1', $this->jsonFile));
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(7200);

        $cv = json_decode(file_get_contents($this->jsonFile), true, 512, \JSON_THROW_ON_ERROR);

        if ($response->isNotModified($request)) {
            return $response;
        }

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

        // Set defaults
        $cv += [
            'work' => [],
            'volunteer' => [],
            'education' => [],
            'awards' => [],
            'skills' => [],
            'languages' => [],
            'interests' => [],
            'profiles' => [],
            'references' => [],
            'profiles' => $profiles,
        ];

        $response->setContent($this->twig->render('about/cv.html.twig', $cv));

        return $response;
    }
}
