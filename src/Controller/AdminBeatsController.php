<?php

namespace App\Controller;

use App\Entity\Beats;
use App\Form\BeatsType;
use App\Repository\BeatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/beats')]
class AdminBeatsController extends AbstractController
{
    #[Route('/liste-beats', name: 'app_admin_beats_index', methods: ['GET'])]
    public function index(BeatsRepository $beatsRepository): Response
    {
        return $this->render('admin_beats/index.html.twig', [
            'beats' => $beatsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_beats_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        $beat = new Beats();
        $form = $this->createForm(BeatsType::class, $beat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageName')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                }catch(FileException $e){

                }
            }
        
            $beat->setImageName($newFilename);

            $audioFile = $form->get('beatName')->getData();
            if ($audioFile){
                $originalFilename = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$audioFile->guessExtension();

                try {
                    $audioFile->move(
                        $this->getParameter('beats_directory'),
                        $newFilename
                    );
                }catch(FileException $e){

                }
            }
            $beat->setBeatName($newFilename);
            $entityManager->persist($beat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_beats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_beats/new.html.twig', [
            'beat' => $beat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_beats_show', methods: ['GET'])]
    public function show(Beats $beat): Response
    {
        return $this->render('admin_beats/show.html.twig', [
            'beat' => $beat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_beats_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Beats $beat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BeatsType::class, $beat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_beats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_beats/edit.html.twig', [
            'beat' => $beat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_beats_delete', methods: ['POST'])]
    public function delete(Request $request, Beats $beat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($beat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_beats_index', [], Response::HTTP_SEE_OTHER);
    }
}
