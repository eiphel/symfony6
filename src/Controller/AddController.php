<?php

namespace App\Controller;

use App\Entity\Add;
use App\Form\AddType;
use App\Repository\AddRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/add')]
class AddController extends AbstractController
{
    #[Route('/', name: 'add_index', methods: ['GET'])]
    public function index(AddRepository $addRepository): Response
    {
        return $this->render('add/index.html.twig', [
            'adds' => $addRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'add_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $add = new Add();
        $form = $this->createForm(AddType::class, $add);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($add);
            $entityManager->flush();

            return $this->redirectToRoute('add_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('add/new.html.twig', [
            'add' => $add,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'add_show', methods: ['GET'])]
    public function show(Add $add): Response
    {
        return $this->render('add/show.html.twig', [
            'add' => $add,
        ]);
    }

    #[Route('/{id}/edit', name: 'add_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Add $add, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddType::class, $add);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('add_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('add/edit.html.twig', [
            'add' => $add,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'add_delete', methods: ['POST'])]
    public function delete(Request $request, Add $add, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$add->getId(), $request->request->get('_token'))) {
            $entityManager->remove($add);
            $entityManager->flush();
        }

        return $this->redirectToRoute('add_index', [], Response::HTTP_SEE_OTHER);
    }
}
