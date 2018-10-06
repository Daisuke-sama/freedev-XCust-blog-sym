<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Creator;
use App\Form\CreatorFormType;


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
    private $objectRepository;

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
        $this->objectRepository   = $entityManager->getRepository('App:Creator');
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
        if ($this->objectRepository->findOneBy(['username' => $this->getUser()->getUserName()])) {
            $this->addFlash('error', 'Unable to add creator because he (or she) already exists.');

            return $this->redirectToRoute('homepage');
        }

        $creator = new Creator();
        $creator->setUsername($this->getUser()->getUsername());

        $form = $this->createForm(CreatorFormType::class, $creator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($creator);
            $this->entityManager->flush();

            $this->addFlash('success', 'Thank you for being registered with this website.');

            $request->getSession()->set('user_is_creator', true);

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
