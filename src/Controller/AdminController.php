<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\EntryFormType;
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
 *
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    /**
     * @var ObjectRepository
     */
    private $_creatorRepository;

    /**
     * @var ObjectRepository
     */
    private $_blogPostRepository;


    /**
     * AdminController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_entityManager      = $entityManager;
        $this->_creatorRepository  = $entityManager->getRepository('App:Creator');
        $this->_blogPostRepository = $entityManager->getRepository('App:BlogPost');
    }

    /**
     * @Route("/admin/creator", name="creator_add")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCreatorAction(Request $request)
    {
        if ($this->_creatorRepository->findOneBy(['username' => $this->getUser()->getUsername()])) {
            $this->addFlash('error', 'Невозможно добавить ещё одного админа. Ты уже есть.');

            return $this->redirectToRoute('homepage');
        }

        $creator = new Creator();
        $creator->setUsername($this->getUser()->getUsername());

        $form = $this->createForm(CreatorFormType::class, $creator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_entityManager->persist($creator);
            $this->_entityManager->flush($creator);

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

    /**
     * @Route("/make-entry", name="admin_make_entry")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function makeEntryAction(Request $request)
    {
        $blogPost = new BlogPost();

        $creator = new $this->creatorRepository->findOneByUsername($this->getUser()->getUsername());
        $blogPost->setCreator($creator);

        $form = $this->createForm(EntryFormType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_entityManager->persist($blogPost);
            $this->_entityManager->flush($blogPost);

            $this->addFlash('success', 'Запись сохранена.');

            return $this->redirectToRoute('creator_add');
        }

        return $this->render(
            'admin/entry_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/", name="admin_index")
     * @Route("/entries", name="admin_entries")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entriesAction()
    {
        $creator = $this->_creatorRepository->findOneByUsername($this->getUser()->getUsername());

        $blogPosts = [];

        if ($creator) {
            $blogPosts = $this->_blogPostRepository->findByCreator($creator);
        }

        return $this->render(
            'admin/entries.html.twig',
            [
                'blogPosts' => $blogPosts,
            ]
        );
    }

    /**
     * @Route("/remove-entry/{entryId}", name="admin_remove_entry")
     *
     * @param $entryId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeEntryAction($entryId)
    {
        $blogPost = $this->_blogPostRepository->findOneById($entryId);
        $creator  = $this->_creatorRepository->findOneByUsername($this->getUser()->getUsername());

        if ( ! $blogPost || $creator !== $blogPost->getCreator()) {
            $this->addFlash('error', "Запись не удаляется.");

            return $this->redirectToRoute("admin_entries");
        }

        $this->_entityManager->remove($blogPost);
        $this->_entityManager->flush();

        $this->addFlash('success', "Запись удалена.");

        return $this->redirectToRoute('admin_entries');
    }
}
