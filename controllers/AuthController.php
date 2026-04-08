<?php

namespace App\Controllers;

use App\Services\Database;
use App\Models\User;
use App\Repository\AdminRepository;
use PDO;

class AuthController extends AbstractController {
    
    // logic de login
    public function login(): void {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // handle le submit du form
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                // search l'admin en db
                $adminRepo = new AdminRepository();
                $admin = $adminRepo->findByEmail($email);

                // verify le password hash
                if ($admin && password_verify($password, $admin->getPassword())) {
                    // save en session
                    $_SESSION['user'] = [
                        'id' => $admin->getId(),
                        'email' => $admin->getEmail()
                    ];
                    $this->setFlash('success', 'Bienvenue Admin');
                    $this->redirect('admin-dashboard');
                    return;
                } else {
                    $this->setFlash('error', 'Identifiants incorrects.');
                }
            }

            // render la page login
            $this->render('login', [
                'title' => 'Connexion Admin'
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
            $this->render('login', ['title' => 'Connexion Admin']);
        }
    }

    // logic de logout
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // clear la session
        $_SESSION = [];
        session_destroy();
        $this->redirect('home');
    }
}
