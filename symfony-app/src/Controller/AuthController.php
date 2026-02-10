<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/auth/{username}/{token}', name: 'auth_login')]
    public function login(string $username, string $token, Connection $connection, Request $request): Response
    {
        //najpierw sprawdźmy czy jest taki user i zwrocmy jego ID
        $sql = "SELECT * FROM users WHERE username = :username ";
        $userData = $connection->fetchAssociative($sql, [
            'username' => $username
        ]);

        if (!$userData) {
            return new Response('Wrong username', 401);
        }
    
        //teraz token ale weźmy też id usera
        $sql = "SELECT * FROM auth_tokens WHERE token = :token and user_id = :user_id";
        $tokenData = $connection->fetchAssociative($sql, [
            'token' => $token,
            'user_id' => $userData['id']
        ]);

        if (!$tokenData) {
            return new Response('Invalid token', 401);
        }

        $session = $request->getSession();
        $session->set('user_id', $userData['id']);
        $session->set('username', $username);

        $this->addFlash('success', 'Welcome back, ' . $username . '!');

        return $this->redirectToRoute('home');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        $this->addFlash('info', 'You have been logged out successfully.');

        return $this->redirectToRoute('home');
    }
}
