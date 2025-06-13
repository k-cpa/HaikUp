<?php
namespace App\Twig;

use App\Service\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private NotificationService $notificationService,
        private Security $security
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_notifs', [$this, 'getUnreadCount']),
        ];
    }

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();

        if (!$user) {
            return 0;
        }

        return $this->notificationService->countUnread($user);
    }
}
