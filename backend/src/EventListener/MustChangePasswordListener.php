<?php

namespace App\EventListener;

use App\Entity\Admin;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Forces a fresh install's initial admin (see CreateAdminCommand) to change
 * their generated password before reaching anything else. gui_users already
 * has the change-password form, so that's the one page left reachable.
 */
class MustChangePasswordListener implements EventSubscriberInterface
{
    private const ALLOWED_PATH_PREFIXES = [
        '/gui_users',
        '/api/change_password',
        '/api/show_users',
        '/logout',
        '/css/',
        '/js/',
        '/media/',
    ];

    public function __construct(private readonly Security $security)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof Admin || !$user->isMustChangePassword()) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();
        foreach (self::ALLOWED_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return;
            }
        }

        $event->setResponse(new RedirectResponse('/gui_users'));
    }
}
