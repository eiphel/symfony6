<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Form\AdCategoryType;
use App\Repository\AdCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ad/category')]
class AdCategoryController extends AbstractController
{
    #[Route('/', name: 'ad_category_index', methods: ['GET'])]
    public function index(AdCategoryRepository $adCategoryRepository): Response
    {
        return $this->render('ad_category/index.html.twig', [
            'ad_categories' => $adCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'ad_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adCategory = new AdCategory();
        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adCategory);
            $entityManager->flush();

            return $this->redirectToRoute('ad_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ad_category/new.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ad_category_show', methods: ['GET'])]
    public function show(AdCategory $adCategory): Response
    {
        return $this->render('ad_category/show.html.twig', [
            'ad_category' => $adCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'ad_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdCategory $adCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ad_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ad_category/edit.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ad_category_delete', methods: ['POST'])]
    public function delete(Request $request, AdCategory $adCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_category_index', [], Response::HTTP_SEE_OTHER);
    }

}
