<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/{_locale}/home', name: 'home', requirements:['_locale' => 'fr|en'])]
    public function index(TranslatorInterface $translator, Request $request): Response
    {
        //$locale = $request->getLocale();
        //dump($locale);
        //dd($translator->trans('Symfony.great'));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}