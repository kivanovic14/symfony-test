<?php


namespace App\Controller;


use App\Entity\Notification;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security ("is_granted('ROLE_USER')")
 * @Route ("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    public $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount(): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return new JsonResponse([
            'count' => $this->notificationRepository->findUnseenByUser($currentUser)
        ]);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notifications()
    {
        // lets users to see all the notifications that have been unseen by them yet
        return $this->render('notification/notifications.html.twig', [
            'notifications' => $this->notificationRepository->findBy([
                'seen' => false,
                'user' => $this->getUser()
            ])
        ]);
    }

    /**
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     * @param Notification $notification
     * @return RedirectResponse
     */
    public function acknowledge(Notification $notification): RedirectResponse
    {
        // acknowledge = prihvatiti(priznati)
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     */
    public function acknowledgeAll()
    {
        $this->notificationRepository->markAllAsReadByUser($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }
}