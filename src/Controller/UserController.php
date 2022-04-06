<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Beats;
use App\Entity\Commande;
use App\Form\UserInfoType;
use App\Form\BeatsUserType;
use App\Repository\UserRepository;
use App\Repository\BeatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/user/profil', name: 'user')]
    public function index(): Response
    {
        if(!$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        return $this->render('user/index.html.twig');
    }

    #[Route('/user/profil/{id}', name: 'userView')]
    public function view($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/info', name: 'info')]
    public function info(UserRepository $userRepository): Response
    {
        $user = $userRepository->find($this->getUser());
        return $this->render('user/info.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/edit-info', name: 'app_user_user_edit', methods: ['GET', 'POST'])]
    public function editInfo(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        if(!$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        $user = $userRepository->find($this->getUser());
        $form = $this->createForm(UserInfoType::class, $user);
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
        
            $user->setImageName($newFilename);
            $entityManager->flush();

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_edit_info/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/beats', name: 'app_user_beats_index', methods: ['GET'])]
    public function listAll(BeatsRepository $beatsRepository): Response
    {
        if(!$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        return $this->render('list_beats/index.html.twig', [
            'beats' => $beatsRepository->findByUser($this->getUser()),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/user/show/{id}', name: 'app_user_beats_index_view', methods: ['GET'])]
    public function viewBeats($id, BeatsRepository $beatsRepository, UserRepository $userRepository): Response
    {
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        return $this->render('list_beats/index.html.twig', [
            'beats' => $beatsRepository->findByUser($id),
            'user' => $userRepository->find($id),
            'panier' => $orderBeats,
        ]);
    }
    
    #[Route('/user/new', name: 'app_user_beats_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BEATMAKER')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        $beat = new Beats();
        $form = $this->createForm(BeatsUserType::class, $beat);
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

            $beat->setUser($this->getUser());
            $entityManager->persist($beat);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_beats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('list_beats/new.html.twig', [
            'beat' => $beat,
            'form' => $form,
        ]);
    }

    #[Route('/user/show/{id}', name: 'app_user_beats_show', methods: ['GET'])]
    #[IsGranted('ROLE_BEATMAKER')]
    public function show(Beats $beat): Response
    {

        if($beat->getUser()==$this->getUser()){
            return $this->render('list_beats/show.html.twig', [
            'beat' => $beat,
        ]);
        }else{
            return $this->redirectToRoute('app_user_beats_index', [], Response::HTTP_SEE_OTHER);
        };
        
    }

    #[Route('/user/edit/{id}', name: 'app_user_beats_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BEATMAKER')]
    public function edit(Request $request, Beats $beat, EntityManagerInterface $entityManager, SluggerInterface $sluggerInterface): Response
    {
        if(!$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        if($beat->getUser()!=$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        $form = $this->createForm(BeatsUserType::class, $beat);
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
            $entityManager->flush();

            return $this->redirectToRoute('app_user_beats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('list_beats/edit.html.twig', [
            'beat' => $beat,
            'form' => $form,
        ]);
    }

    #[Route('user/delete/{id}', name: 'app_user_beats_delete', methods: ['POST'])]
    #[IsGranted('ROLE_BEATMAKER')]
    public function delete(Request $request, Beats $beat, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        if($beat->getUser()!=$this->getUser()) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        if ($this->isCsrfTokenValid('delete'.$beat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($beat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_beats_index', [], Response::HTTP_SEE_OTHER);
    }
}