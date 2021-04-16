<?php


namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(MicroPostRepository $microPostRepository,
    FormFactoryInterface $formFactory,EntityManagerInterface $entityManager,
    RouterInterface $router, FlashBagInterface $flashBag, AuthorizationCheckerInterface $authorizationChecker)
    {

        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }
    /**
     * @Route("/", name="micro_post_index")
     */

    public function index(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $currentUser = $tokenStorage->getToken()->getUser();

        $usersToFollow=[];

        if ($currentUser instanceof User)  // this means that the current user is authenticated
        {
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ?
                $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser): [];
        }
        else{
            // current user isn't authenticated
            $posts = $this->microPostRepository->findBy(
                [],
                ['time'=>'DESC']
            );
        }
        return $this->render('micro-post/index.html.twig',
            [
                'posts'=> $posts,
                'usersToFollow'=>$usersToFollow
            ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @param MicroPost $microPost
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response


     */
    public function edit(MicroPost $microPost, Request $request,$id)
    {
//        $this->denyAccessUnlessGranted('edit', $microPost);

//        if($this->authorizationChecker->isGranted('edit', $microPost)){
//            throw new UnauthorizedHttpException();
//        }
        $microPost=$this->getDoctrine()->getRepository(MicroPost::class)->find($id);
        $form=$this->createFormBuilder($microPost)
            ->add('text',TextareaType::class,['label'=>false])
            ->add('save',SubmitType::class)->getForm();
        //$form=$this->formFactory->create(MicroPostType::class,$microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }
        return $this->render('micro-post/add.html.twig',
        ['form'=>$form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")

     */
    public function delete(Request $request,$id)
    {
        //        if($this->authorizationChecker->isGranted('delete', $microPost)){
//            throw new UnauthorizedHttpException();
//        }

        $microPost=$this->getDoctrine()->getRepository(MicroPost::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($microPost);
        $entityManager->flush();
        $this->flashBag->add('notice','Micro post was deleted');

        return new RedirectResponse($this->router->generate('micro_post_index'));
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return RedirectResponse|Response


     */
    public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $microPost=new MicroPost();
        $microPost->setUser($user);
        $form=$this->formFactory->create(MicroPostType::class,$microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }
        return $this->render('micro-post/add.html.twig',
        ['form'=>$form->createView()]);
    }


    /**
     * @Route("/{id}", name="micro_post_post")
     * @param $id
     * @return Response
     */

    public function post($id): Response
    {
        $post=$this->microPostRepository->find($id);

        return $this->render('micro-post/post.html.twig',
            ['post'=>$post]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $userWithPosts
     * @return Response
     */

    public function userPosts (User $userWithPosts): Response
        // displays all the posts from the certain user
    {
        $posts = $this->microPostRepository->findBy(
            ['user'=>$userWithPosts],
            ['time'=> 'DESC']
        );
        return $this->render('micro-post/user-posts.html.twig',
            [
                'posts'=> $posts,

                'user'=> $userWithPosts
            ]
        );
    }

}