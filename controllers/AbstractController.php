<?php

namespace App\Controllers;

abstract class AbstractController {
    
    // helper pour render une view avec un layout
    protected function render(string $view, array $data = []): void {
        $profileRepo = new \App\Repository\ProfileRepository();
        $data['profile'] = $profileRepo->findOne();
        
        // extract les vars pour la view
        extract($data);
        $content = $view;
        $layoutPath = __DIR__ . '/../views/layout.phtml';

        if (file_exists($layoutPath)) {
            // require le layout file
            require $layoutPath; 
        } else {
            die("Le layout (.phtml) n'existe pas.");
        }
    }

    // helper pour redirect simple
    protected function redirect(string $action): void {
        header("Location: index.php?action=$action");
        exit();
    }

    // flash messages pour feedback user
    protected function setFlash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }
}
