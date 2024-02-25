<?php

namespace App\Controller;

use App\Entity\Timesheet;
use App\Form\Timesheet1Type;
use App\Repository\TimesheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/timesheet')]
class TimesheetController extends AbstractController
{
    #[Route('/', name: 'app_timesheet_index', methods: ['GET'])]
    public function index(TimesheetRepository $timesheetRepository): Response
    {
        return $this->render('timesheet/index.html.twig', [
            'timesheets' => $timesheetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_timesheet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timesheet = new Timesheet();
        $form = $this->createForm(Timesheet1Type::class, $timesheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($timesheet);
            $entityManager->flush();

            return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timesheet/new.html.twig', [
            'timesheet' => $timesheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timesheet_show', methods: ['GET'])]
    public function show(Timesheet $timesheet): Response
    {
        return $this->render('timesheet/show.html.twig', [
            'timesheet' => $timesheet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_timesheet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Timesheet $timesheet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Timesheet1Type::class, $timesheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timesheet/edit.html.twig', [
            'timesheet' => $timesheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timesheet_delete', methods: ['POST'])]
    public function delete(Request $request, Timesheet $timesheet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timesheet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($timesheet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_timesheet_index', [], Response::HTTP_SEE_OTHER);
    }
}
