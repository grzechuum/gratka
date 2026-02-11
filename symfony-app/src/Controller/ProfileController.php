<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Service\PhoenixApiService;
use Doctrine\DBAL\Connection;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function profile(Request $request, EntityManagerInterface $em, PhoenixApiService $phoenixApi, Connection $connection): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        $user = $em->getRepository(User::class)->find($userId);
        //dump($user->getTokenPhoenixApi());

        if (!$user) {
            $session->clear();
            return $this->redirectToRoute('home');
        }

        $form = $this->createFormBuilder($user)
            ->add('token_phoenix_api', TextType::class, [
                'label' => 'Access token to PhoenixApi'
            ])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('fetch', SubmitType::class, [
                'label' => 'Import from Phoenix',
                'attr' => [
                    'class' => 'btn-secondary'
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $em->persist($user); 
                $em->flush();

                $this->addFlash('success', 'Token saved!');
                
                return $this->redirectToRoute('profile');
            }

            if ($form->get('fetch')->isClicked()) {
                $response = $phoenixApi->getPhotos(
                    $user->getTokenPhoenixApi()
                );
                if(isset($response['authorized']) && !$response['authorized']){
                    $this->addFlash('error', 'Token error!');
                } elseif(count((array)$response['photos'])>0){
                    //dump($response['photos']);
                    $imported=0;
                    foreach((array)$response['photos'] as $f){
                        $sql = "SELECT count(*) as num FROM photos WHERE image_url = :image_url and user_id = :user_id";
                        $ph = $connection->fetchAssociative($sql, [
                            'image_url' => $f['photo_url'],
                            'user_id' => $userId
                        ]);
                        if($ph['num']==0){
                            $imported++;
                            $connection->insert('photos', [
                                'user_id' => $userId,
                                'image_url' => $f['photo_url'],
                                'taken_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                    $this->addFlash('success', 'Imported photos: '.$imported);
                } else {
                    $this->addFlash('success', 'No photos.');
                }
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
