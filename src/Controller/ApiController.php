<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AdCategoryRepository;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/categories', name: 'api_categories')]
    public function categories(Request $request, AdCategoryRepository $repository) : Response
    {
        $data = [];

        $q = $request->get('q');

        if (strlen($q) >= 1) {
            $categories = $repository->search($q);

            foreach($categories as $category) {
                array_push($data, ['id' => $category->getId(), 'name' => $category->getname()]);
            }
    
        }

        return $this->json($data);
    }
}
