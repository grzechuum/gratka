<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Likes\LikeRepository;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return JsonResponse
     */
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $managerRegistry): Response
    {
        $photoRepository = new PhotoRepository($managerRegistry);
        $likeRepository = new LikeRepository($managerRegistry);

        $form = $this->createFormBuilder()
            ->add('sort', ChoiceType::class, [
                'label' => 'Sort',
                'choices' => [
                    'Location (A-Z)' => 'location_asc',
                    'Location (Z-A)' => 'location_desc',
                    'Camera (A-Z)' => 'camera_asc',
                    'Camera (Z-A)' => 'camera_desc',
                    'Description (A-Z)' => 'description_asc',
                    'Description (Z-A)' => 'description_desc',
                    'Taken at (latest)' => 'taken_at_desc',
                    'Taken at (oldest)' => 'taken_at_asc',
                    'Username (A-Z)' => 'username_asc',
                    'Username (Z-A)' => 'username_desc',
                ],
                'mapped' => false,
                'required' => false,
                'placeholder' => 'sort by',
                'attr' => [
                    'class' => 'select-filter'
                ]
            ])
            ->add('filter', SubmitType::class, [
                'label' => 'Sort',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        $sort = 'p.id'; 
        $order = 'ASC';

        if ($form->isSubmitted()) {
            $sort = $form->get('sort')->getData();
            switch ($sort) {
                case 'location_asc':
                    $sort = 'p.location'; 
                    $order = 'ASC';
                    break;
                case 'location_desc':
                    $sort = 'p.location'; 
                    $order = 'DESC';
                    break;
                case 'camera_asc':
                    $sort = 'p.camera'; 
                    $order = 'ASC';
                    break;
                case 'camera_desc':
                    $sort = 'p.camera'; 
                    $order = 'DESC';
                    break;
                case 'description_asc':
                    $sort = 'p.description'; 
                    $order = 'ASC';
                    break;
                case 'description_desc':
                    $sort = 'p.description'; 
                    $order = 'DESC';
                    break;
                case 'taken_at_asc':
                    $sort = 'p.taken_at'; 
                    $order = 'ASC';
                    break;
                case 'taken_at_desc':
                    $sort = 'p.taken_at'; 
                    $order = 'DESC';
                    break;
                case 'username_asc':
                    $sort = 'u.username'; 
                    $order = 'ASC';
                    break;
                case 'username_desc':
                    $sort = 'u.username'; 
                    $order = 'DESC';
                    break;
            
                default:
                    $sort = 'p.id'; 
                    $order = 'ASC';
            }
        } 

        $photos = $photoRepository->findAllWithUsers($sort, $order);

        $session = $request->getSession();
        $userId = $session->get('user_id');
        $currentUser = null;
        $userLikes = [];

        if ($userId) {
            $currentUser = $em->getRepository(User::class)->find($userId);

            if ($currentUser) {
                foreach ($photos as $photo) {
                    $likeRepository->setUser($currentUser);
                    $userLikes[$photo->getId()] = $likeRepository->hasUserLikedPhoto($photo);
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'currentUser' => $currentUser,
            'userLikes' => $userLikes,
            'form' => $form->createView(),
        ]);
    }
}
