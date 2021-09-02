<?php

namespace App\Controller\Admin;

use App\Repository\RestaurantRepository;
use App\Repository\UsersRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Restaurant;
use App\Entity\Users;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;

class DashboardController extends AbstractDashboardController
{
    
    protected $usersRespository;
    protected $restaurantRepository;

    public function __construct(UsersRepository $usersRespository,RestaurantRepository $restaurantRepository)
    {
        $this->usersRepository = $usersRespository;
        $this->restaurantRepository = $restaurantRepository;

    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/welcome.html.twig', [

            'Users' =>  $this->usersRepository ->findAll(),
            'Restaurant' => $this->restaurantRepository->findAll()
        ]);
        //return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CAMPUS-RESTO');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Restaurants', 'fas fa-hamburger', Restaurant::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', Users::class);
    }

    //syteme pour gerer les gravatar
    
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
        ->setName($user->getUserIdentifier())
        ->setGravatarEmail($user->getUserIdentifier())
       // ->setAvatarUrl('') pour donner des liens par exemple le github;
        ->displayUserAvatar('true');
    }
}
