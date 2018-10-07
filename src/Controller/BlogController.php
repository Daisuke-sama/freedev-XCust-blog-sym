<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $_enityManager;

    /**
     * @var ObjectRepository
     */
    private $_creatorRepository;

    /**
     * @var ObjectRepository
     */
    private $_blogPostRepository;

    /**
     * BlogController constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->_enityManager = $manager;
        $this->_blogPostRepository = $this->_enityManager->getRepository('App:BlogPost');
        $this->_creatorRepository = $this->_enityManager->getRepository('App:Creator');
    }

    /**
     * @Route("/", name="homepage")
     * @Route("/entries", name="entries")
     */
    public function entriesAction()
    {
        return $this->render('blog/entries.html.twig', [
            'blogPost' => $this->_blogPostRepository->findAll()
        ]);
    }
}
