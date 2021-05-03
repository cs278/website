<?php

namespace App\Controller;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FaviconController
{
    private Packages $assets;

    public function __construct(Packages $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @Route("/favicon.ico")
     */
    public function __invoke(Request $request): Response
    {
        $iconPath = $this->assets->getUrl('assets/glider.svg');
        $documentRoot = $request->server->get('DOCUMENT_ROOT');

        // If file can be found locally return it with cache headers, otherwise
        // redirec the browser to the URL.
        if (stream_is_local($iconPath) && is_file($documentRoot.$iconPath)) {
            $response = new BinaryFileResponse($documentRoot.$iconPath, 200);
            $response->setPublic();
            $response->setMaxAge(604800);
            $response->setSharedMaxAge(2419200);

            return $response;
        }

        $response = new RedirectResponse($iconPath);
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
