<?php
/**
 * Created by Pavel Burylichau
 * Company: EPAM Systems
 * User: pavel_burylichau@epam.com
 * Date: 10/7/18
 * Time: 1:19 AM
 */


namespace App\EventListener\Creator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CheckIsCreatorListener for redirect after a creator instantiation.
 *
 * @package App\EventListener\Creator
 */
class CheckIsCreatorListener
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TokenStorageInterface
     */
    private $_tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    /**
     * CheckIsCreatorListener constructor.
     *
     * @param RouterInterface        $router
     * @param SessionInterface       $session
     * @param TokenStorageInterface  $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RouterInterface $router,
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager
    ) {
        $this->router         = $router;
        $this->session        = $session;
        $this->_tokenStorage  = $tokenStorage;
        $this->_entityManager = $entityManager;
    }

    /**
     * On kernel.controller
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        // If the path does not begin with /admin, then don't add to flasher
        if ( ! preg_match('/^\/admin/i', $event->getRequest()->getPathInfo())) {
            return;
        }

        $user = $this->_tokenStorage->getToken()->getUser();
        if (null === $user) {
            return;
        }

        // For exit this listener early, when the relevant checks were made
        if (true === $this->session->get('user_is_creator')) {
            return;
        }

        // For check a new attempt to add a creator
        $route = $this->router->generate('creator_add');
        if (0 === strpos($event->getRequest()->getPathInfo(), $route)) {
            return;
        }

        // Does the authenticated user is a creator
        $creator = $this->_entityManager
            ->getRepository('App:Creator')
            ->findOneByUsername($user->getUsername());
        if ($creator) {
            $this->session->set('user_is_creator', true);
        }

        if ( ! $creator && $this->session->get('pending_user_is_creator')) {
            $this->session->getFlashBag()->add(
                'warning',
                'Доступ администратора установлен. Подожди секунд 30 и попробуй снова.'
            );
            $route = $this->router->generate('homepage');
        } else {
            $this->session->getFlashBag()->add(
                'warning',
                'Стань админом и получи доступ. Это может каждый сейчас. Зарегистрируйся.'
            );
        }

        $event->setController(
            function () use ($route) {
                return new RedirectResponse($route);
            }
        );
    }
}