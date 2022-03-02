<?php

namespace App\Controller;

use App\Entity\Email;
use App\Service\SendEmail;
use App\Form\ContactType;
use App\Message\SendEmailMessage;
use App\Repository\EmailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/contact')]
class ContactController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('/', name: 'contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): Response
    {
        $email = new Email();
        $form = $this->createForm(ContactType::class, $email);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $email->setIp($request->getClientIp());
            $email->setApp('contact');

            $entityManager->persist($email);
            $entityManager->flush();
            //  symfony console messenger:consume async -vv
            $messageBus->dispatch(new SendEmailMessage($email));

            $request->getSession()
                ->getFlashBag()
                ->add('success', $this->translator->trans('Congratulations! Your message has been successfully sent.', [], 'message'));

            return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/new.html.twig', [
            'email' => $email,
            'form' => $form,
        ]);
    }

    #[Route('/list', name: 'contact_index', methods: ['GET'])]
    public function index(EmailRepository $emailRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'emails' => $emailRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'contact_show', methods: ['GET'])]
    public function show(Email $email): Response
    {
        return $this->render('contact/show.html.twig', [
            'email' => $email,
        ]);
    }

    #[Route('/{id}/edit', name: 'contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Email $email, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'email' => $email,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'contact_delete', methods: ['POST'])]
    public function delete(Request $request, Email $email, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$email->getId(), $request->request->get('_token'))) {
            $entityManager->remove($email);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
