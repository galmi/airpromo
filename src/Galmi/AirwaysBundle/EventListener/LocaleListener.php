<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 04.08.15
     * Time: 13:27
     */

namespace Galmi\AirwaysBundle\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /** @var array */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct($locales = ['en'])
    {
        $this->locales = $locales;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
            KernelEvents::RESPONSE => array(array('onKernelResponse', 17)),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->cookies->get('locale')) {
            $request->setLocale($locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getPreferredLanguage($this->locales));
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $requestLocale = $request->getLocale();
        if ($requestLocale != $request->cookies->get('locale')) {
            $response->headers->setCookie(new Cookie('locale', $request->getLocale(), 0, '/', null, false, false));
        }
    }
}
