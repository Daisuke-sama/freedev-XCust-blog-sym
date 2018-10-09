<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * The limit for posts when fetching.
     */
    const LIMIT = 10;

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
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entriesAction(Request $request)
    {
        $page = 1;

        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('blog/entries.html.twig', [
            'blogPosts' => $this->_blogPostRepository->getAllPosts($page, self::LIMIT),
            'totalBlogPosts' => $this->_blogPostRepository->getPostCount(),
            'page' => $page,
            'limit' => self::LIMIT,
        ]);
    }
}
