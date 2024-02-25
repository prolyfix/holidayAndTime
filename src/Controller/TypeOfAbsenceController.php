<?php

namespace App\Controller;

use App\Entity\TypeOfAbsence;
use App\Form\TypeOfAbsenceType;
use App\Repository\TypeOfAbsenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/of/absence')]
class TypeOfAbsenceController extends AbstractController
{
    #[Route('/', name: 'app_type_of_absence_index', methods: ['GET'])]
    public function index(TypeOfAbsenceRepository $typeOfAbsenceRepository): Response
    {
        return $this->render('type_of_absence/index.html.twig', [
            'type_of_absences' => $typeOfAbsenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_of_absence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeOfAbsence = new TypeOfAbsence();
        $form = $this->createForm(TypeOfAbsenceType::class, $typeOfAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeOfAbsence);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_of_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_of_absence/new.html.twig', [
            'type_of_absence' => $typeOfAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_of_absence_show', methods: ['GET'])]
    public function show(TypeOfAbsence $typeOfAbsence): Response
    {
        return $this->render('type_of_absence/show.html.twig', [
            'type_of_absence' => $typeOfAbsence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_of_absence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeOfAbsence $typeOfAbsence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeOfAbsenceType::class, $typeOfAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_of_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_of_absence/edit.html.twig', [
            'type_of_absence' => $typeOfAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_of_absence_delete', methods: ['POST'])]
    public function delete(Request $request, TypeOfAbsence $typeOfAbsence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeOfAbsence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($typeOfAbsence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_of_absence_index', [], Response::HTTP_SEE_OTHER);
    }
}
