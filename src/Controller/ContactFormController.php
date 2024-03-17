<?php

namespace App\Controller;

use App\Entity\ContactForm;
use App\Repository\ContactFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactFormController extends AbstractController
{
    #[Route('/contactform', name: 'create_contact_form')]
    public function createContactForm(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $contactForm = new ContactForm();
        $contactForm->setName('Janko');
        $contactForm->setSurName('Muzykant');
        $contactForm->setEmail('janko.muzykant@tempmail.temp');
        $contactForm->setContents('Polecam się na przyszłość i pozdrawiam gorąco.');

        $errors = $validator->validate($contactForm);
        if (count($errors) > 0) {
            dd($errors);
            return $this->json([
                'message' => 'Niestety, przy próbie zapisu wystąpiły błędy.',
                'errors' =>  $errors,
                'success' => false,
            ]);
        }

        $entityManager->persist($contactForm);
        $entityManager->flush();

        return $this->json([
            'message' => 'Dane z formularza zostały poprawnie zapisane!',
            'contact_form' => [
                $contactForm->getName(), 
                $contactForm->getSurname(), 
                $contactForm->getEmail(), 
                $contactForm->getContents()
            ],
        ]);
    }

    #[Route('/contactforms', name: 'contact_form_list')]
    public function list(ContactFormRepository $contactFormRepository): JsonResponse
    {
        $contactForms = $contactFormRepository->findAll();

        if (!$contactForms) {
            throw $this->createNotFoundException(
                'Brak danych w bazie'
            );
        }

        return $this->json([
            'success' => true,
            'contact_forms' => $contactForms,
        ]);
    }

    #[Route('/arrlist', name: 'contact_form_array')]
    public function arraylist(ContactFormRepository $contactFormRepository): JsonResponse
    {
        $contactForms = $contactFormRepository->findAll();

        if (!$contactForms) {
            throw $this->createNotFoundException(
                'Brak danych w bazie'
            );
        }

        return new JsonResponse([
            'success' => true,
            'contact_forms' => json_encode(json_decode($contactForms), true),
        ]);
    }
}
