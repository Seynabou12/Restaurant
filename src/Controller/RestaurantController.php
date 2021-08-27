<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use Doctrine\ORM\EntityManager;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\PlatsRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    #[Route('/', name: 'restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $restaurant = new Restaurant();
        
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurant);
            $em->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form
        ]);
    }

    #[Route('/show/{id}', name: 'restaurant_show', methods: ['GET' , 'POST'])]
    public function show(Restaurant $restaurant, Request $request, EntityManagerInterface $em): Response
    {
        $menu = new Menu;

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menu->setRestaurant($restaurant);
            $em->persist($menu);
            $em->flush();

        return $this->redirectToRoute('menu_show', ['id' => $menu->getId()]);
       }
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form ->createView()
        ]);
    }

    #[Route('/affiche/{id}' , name:'affiche_menu')]
    public function affichage(Restaurant $restaurant, MenuRepository $menuRepository, PlatRepository $platRepository)
    {
        //pour récupérer tous les menus de chaque restaurant
        $menu =  $restaurant->getMenus()->getValues()[0];
         //dd($menu);
         $plat = $menu->getPlats()->getValues();
        // dd($plats);
        return $this->renderForm('restaurant/affichage.html.twig', [
            'menus' => $menu,
            'restaurant' => $restaurant,
            'plats' => $plat
        ]);
    }
    
   
    #[Route('/edit/{id}', name: 'restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    
   
    #[Route('/delete/{id}', name: 'restaurant_delete')]
    public function delete(Restaurant $restaurant): Response
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($restaurant);
            $em->flush();

        return $this->redirectToRoute('restaurant_index');
    }
}