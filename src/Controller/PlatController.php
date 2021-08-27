<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/plat')]
class PlatController extends AbstractController
{
    #[Route('/', name: 'plat_index', methods: ['GET'])]
    public function index(PlatRepository $platRepository): Response
    {
        return $this->render('plat/index.html.twig', [
            'plats' => $platRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'plat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'plat_show', methods: ['GET' , 'POST'])]
    public function show(Plat $plat, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment;

        //On génére le formulaire

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        //traitement du formulaire
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            
            //$comment->setCreatedAt(new \DateTime('now'));
            $comment->setPlat($plat);
            //dd($comment);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            
            $this->addFlash('message', 'votre commentaire a bien été envoyé');
            return $this->redirectToRoute('plat_show', ['id' => $plat->getId()]);
        }
    
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
            'commentForm' => $commentForm->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'plat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plat $plat): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'plat_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Plat $plat): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
    }
}
