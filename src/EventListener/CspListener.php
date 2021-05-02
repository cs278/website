<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CspListener implements EventSubscriberInterface
{
    private $policy;
    private $enforce;
    private $reportUri;

    public function __construct($policy, $enforce = false, $reportUri = null)
    {
        $this->policy = is_array($policy) ? implode('; ', $policy) : $policy;
        $this->enforce = (bool) $enforce;
        $this->reportUri = $reportUri;
    }

    public function onKernelResponse(ResponseEvent $e)
    {
        if (!$e->isMasterRequest()) {
            return;
        }

        $response = $e->getResponse();

        if ($response->isRedirect()) {
            return;
        }

        $headerName = $this->enforce
            ? 'Content-Security-Policy'
            : 'Content-Security-Policy-Report-Only';

        if ($this->reportUri) {
            $headerValue = '%1$s report-uri %2$s;';
        } else {
            $headerValue = '%1$s';
        }

        $headerValue = sprintf($headerValue, $this->policy, $this->reportUri);

        $response->headers->set($headerName, $headerValue);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
