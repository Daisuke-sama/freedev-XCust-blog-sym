<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Creator;
use App\Form\CreatorFormType;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class AdminController
 *
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $creatorRepository;

    /**
     * @var ObjectRepository
     */
    private $blogPostRepository;


    /**
     * AdminController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager      = $entityManager;
        $this->creatorRepository  = $entityManager->getRepository('App:Creator');
        $this->blogPostRepository = $entityManager->getRepository('App:BlogPost');
    }

    /**
     * @Route("/admin/creator/add", name="creator_add")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function addCreatorAction(Request $request)
    {
        if ($this->creatorRepository->findOneBy(['username' => $this->getUser()->getUsername()])) {
            $this->addFlash('error', 'Невозможно добавить ещё одного админа. Ты уже есть.');

            return $this->redirectToRoute('homepage');
        }

        $creator = new Creator();
        $creator->setUsername($this->getUser()->getUsername());

        $form = $this->createForm(CreatorFormType::class, $creator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($creator);
            $this->entityManager->flush($creator);

            $request->getSession()->set('user_is_creator', true);
            $this->addFlash('success', 'Молодец - ты зарегистрирован.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'admin/add_creator_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
