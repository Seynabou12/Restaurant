<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Plats;
use App\Form\PlatType;
use App\Form\Menu1Type;
use App\Form\PlatsType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/menu')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'menu_show', methods: ['GET' , 'POST'])]
    public function show(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {    
        $plat = new Plat;
        
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plat->setMenu($menu);
            $em->persist($plat);
            $em->flush();
            
            return $this->redirectToRoute('plat_index', ['id' => $menu->getPlats()]);
       
        }
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
            'plats' => $plat,
            'form' => $form ->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'menu_delete', methods: ['POST'])]
    public function delete(Menu $menu): Response
    {
        
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush();

        return $this->redirectToRoute('menu_index');
    }
}