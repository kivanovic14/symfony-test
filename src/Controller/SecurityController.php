<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    public function __construct()
    {

    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig',
            [
                'last_username'=>$authenticationUtils->getLastUsername(),
                'error'=>$authenticationUtils->getLastAuthenticationError()

            ]);

    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     * @param string $token
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function confirm(string $token, UserRepository  $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->findOneBy([
            'confirmationToken'=>$token
        ]);
        if(null !== $user){
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $entityManager->flush();
        }
        return new Response($this->render('security/confirmation.html.twig',
            [
            'user' => $user
        ]));
    }
}