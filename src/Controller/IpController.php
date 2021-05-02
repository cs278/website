<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IpController
{
    /**
     * @Route("/ip.php")
     */
    public function indexAction(Request $request)
    {
        return new Response($request->getClientIp(), 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
