<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'restaurants' => $restaurants,
            
        ]);
        
    }
    #[Route('admin/edit/{id}', name:'admin_edit', methods: ['GET', 'POST'])]
    public function edit(Restaurant $restaurant, Request $request, EntityManagerInterface $em): Response
    {
            $form = $this->createForm(RestaurantType::class, $restaurant);
            $form->handleRequest($request);
            //gerer la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) { 

            $em->flush();
            $this->addFlash('notice', 'insertion réussie');
            return $this->redirectToRoute('admin');

           
        }
        return $this->render("admin/edit.html.twig", [
            'form' =>$form->createView(), 
            'restaurants'=>$restaurant
            ]);
    }

    #[Route('admin/delete/{id}', name:'admin_delete')]
    public function delete(Restaurant $restaurant,EntityManagerInterface $em)
    {
        $em->remove($restaurant);
        $em->flush();
        $this->addFlash('notice', 'suppression réussie');
        return $this->redirectToRoute('admin_index');
    }

}
