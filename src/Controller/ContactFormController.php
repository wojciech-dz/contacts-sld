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

class ContactFormController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager; 
    }

    #[Route('/contactform', name: 'create_contact_form')]
    public function createContactForm(Request $request): JsonResponse
    {
        $params = $request->getPayload()->all();
        if (!$this->validateContactFormData($params)) {
            return $this->json([
                'success' => false,
                'message' => 'Niestety, przy próbie zapisu wystąpiły błędy.',
            ]);
        }

        $contactForm = $this->saveContactFormData($params);
        return $this->json([
            'message' => 'Dane z formularza zostały poprawnie zapisane!',
            'name' => $contactForm->getName(), 
            'surname' => $contactForm->getSurname(), 
            'email' => $contactForm->getEmail(), 
            'contents' => $contactForm->getContents()
        ]);
    }

    protected function validateContactFormData(array $params): bool
    {
        if (empty($params)) {
            return false;
        }
        foreach (['name', 'surname', 'email', 'contents'] as $key) {
            if (!array_key_exists('name', $params) || empty($params[$key])) {
                return false;
            }
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    protected function saveContactFormData($params) 
    {
        $contactForm = new ContactForm();
        $contactForm->setName($params['name']);
        $contactForm->setSurName($params['surname']);
        $contactForm->setEmail($params['email']);
        $contactForm->setContents($params['contents']);

        $this->entityManager->persist($contactForm);
        $this->entityManager->flush();

        return $contactForm;
    }
}
