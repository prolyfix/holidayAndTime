<?php

namespace App\Controller;

use App\Entity\WorkingGroup;
use App\Form\WorkingGroupType;
use App\Repository\WorkingGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/working/group')]
class WorkingGroupController extends AbstractController
{
    #[Route('/', name: 'app_working_group_index', methods: ['GET'])]
    public function index(WorkingGroupRepository $workingGroupRepository): Response
    {
        return $this->render('working_group/index.html.twig', [
            'working_groups' => $workingGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_working_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workingGroup = new WorkingGroup();
        $form = $this->createForm(WorkingGroupType::class, $workingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($workingGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_working_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('working_group/new.html.twig', [
            'working_group' => $workingGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_working_group_show', methods: ['GET'])]
    public function show(WorkingGroup $workingGroup): Response
    {
        return $this->render('working_group/show.html.twig', [
            'working_group' => $workingGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_working_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkingGroup $workingGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkingGroupType::class, $workingGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_working_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('working_group/edit.html.twig', [
            'working_group' => $workingGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_working_group_delete', methods: ['POST'])]
    public function delete(Request $request, WorkingGroup $workingGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workingGroup->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workingGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_working_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
