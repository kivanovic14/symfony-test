<?php


namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends AbstractController
{
    /**
     * @Route("/follow/{id}", name="following_follow")
     * @param User $userToFollow
     * @return RedirectResponse
     */
    public function follow(User $userToFollow)
    {

        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if($userToFollow->getId() !== $currentUser->getId()){
            $currentUser->follow($userToFollow);
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->redirectToRoute('micro_post_user',
            ['username' => $userToFollow->getUsername()]
        );
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     * @param User $userToUnfollow
     * @return RedirectResponse
     */
    public function unfollow(User $userToUnfollow)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        // delete record from the following table
        $currentUser->getFollowing()->removeElement($userToUnfollow);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('micro_post_user',
            ['username' => $userToUnfollow->getUsername()]
        );
    }
}