<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[Route('/category')]
class CategoryController extends AbstractController
{
    const ROOT = 'CATEGORIES_ROOT';

    #[Route('/{id}', name: 'category_index', defaults: ['id' => null], requirements : ['id' =>"\d+"], methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, Category $category = null): Response
    {
        if (!$category) {
            $category = $categoryRepository->findOneByIdentifier(self::ROOT);
        } 
        
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        $categories = $categoryRepository->getChildren($category, true);  

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'categories' =>  $categories,
        ]);
    }

    #[Route('/new/{id}', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Category $category): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        $parentCategory = $category;

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', ['id' => $category->getParent() ? $category->getParent()->getId() : null], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'parentCategory' => $parentCategory,
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
                       
            return $this->redirectToRoute('category_index', ['id' => $category->getParent() ? $category->getParent()->getId() : null], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'parentCategory' => $category->getParent(),
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/up', name: 'category_up', methods: ['GET'])]
    public function up(Category $category, EntityManagerInterface $em): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        $repo = $em->getRepository(Category::class);
            
        $repo->moveUp($category, 1);

        $id = null === $category->getParent() ? null : $category->getParent()->getId();

        return $this->redirectToRoute('category_index', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/down', name: 'category_down', methods: ['GET'])]
    public function down(Category $category, EntityManagerInterface $em): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        $repo = $em->getRepository(Category::class);
            
        $repo->moveDown($category, 1);

        $id = null === $category->getParent() ? null : $category->getParent()->getId();

        return $this->redirectToRoute('category_index', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        if ($category->getRoot()->getIdentifier() != self::ROOT) {
            throw new HttpException(500, 'Bad identifier');
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->removeFromTree($category);
            $entityManager->clear(); 
        }

        return $this->redirectToRoute('category_index', ['id' => $category->getParent() ? $category->getParent()->getId() : null], Response::HTTP_SEE_OTHER);
    }
}
