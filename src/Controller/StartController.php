<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use Doctrine\Bundle\DoctrineBundle\Controller\ProfilerController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class StartController extends AbstractController
{
    #[Route('/index/start', name: 'index')]
    public function redirectToStartPage(): RedirectResponse
    {
        return $this->redirectToRoute('index_start_newest');
    }

    #[Route('/index/start/delete/{id}/{redirectTo}', name: 'note_delete')]
    public function deleteNote(EntityManagerInterface $entityManager, int $id, $redirectTo): Response
    {
        $noteToDelete = $entityManager->getRepository(Note::class)->findOneBy(['id' => $id]);
        $activeUser = $this->getUser()->getUserIdentifier();

        if ($noteToDelete->getUsername() === $activeUser) {
            $noteToDelete->setDeleted(true);
            $entityManager->flush();
        } else {
            throw new AccessDeniedHttpException("You can only delete your notes.");
        }

        return $this->redirectToRoute($redirectTo);
    }

    #[Route('/index/start/newest', name: 'index_start_newest')]
    public function notesSortByNewest(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->notes($request, $entityManager, 'index_start_newest', 'DESC');
    }

    #[Route('/index/start/oldest', name: 'index_start_oldest')]
    public function notesSortByOldest(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->notes($request, $entityManager, 'index_start_oldest', 'ASC');
    }

    public function notes(Request $request, EntityManagerInterface $entityManager, $redirectTo, $sortOrder): Response|RedirectResponse
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setDeleted(false);
            $note->setUsername($this->getUser()->getUserIdentifier());
            $note->setContent(
                $form->get('content')->getData()
            );

            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute($redirectTo);
        }

        $activeNotes = $entityManager->getRepository(Note::class)->findBy(
            ['deleted' => 0,
                'username' => $this->getUser()->getUserIdentifier()],
            ['id' => $sortOrder],
        );

        return $this->render('index/start.html.twig', [
            'noteForm' => $form,
            'activeNotes' => $activeNotes,
            'username' => $this->getUser()->getUserIdentifier(),
            'currentPage' => $redirectTo
        ]);
    }
}