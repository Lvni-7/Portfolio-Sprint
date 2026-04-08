<?php

namespace App\Services;

use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;

class Routing {
    // handle la request selon l'action
    public function handleRequest(): void {
        // get l'action de l'url
        $action = $_GET['action'] ?? 'home';

        // switch sur l'action pour call le bon controller
        switch ($action) {
            case 'home':
                (new HomeController())->index();
                break;
            case 'project':
            case 'project-details':
                (new ProjectController())->show();
                break;
            case 'login':
                (new AuthController())->login();
                break;
            case 'logout':
                (new AuthController())->logout();
                break;
            case 'admin-dashboard':
                (new AdminController())->index();
                break;
            case 'admin-list':
                (new AdminController())->list();
                break;
            case 'admin-form':
                (new AdminController())->form();
                break;
            case 'admin-save':
                (new AdminController())->save();
                break;
            case 'admin-delete':
                (new AdminController())->delete();
                break;
            case 'category-list':
                (new AdminController())->categoryList();
                break;
            case 'category-form':
                (new AdminController())->categoryForm();
                break;
            case 'category-save':
                (new AdminController())->categorySave();
                break;
            case 'category-delete':
                (new AdminController())->categoryDelete();
                break;
            case 'profile-form':
                (new AdminController())->profileForm();
                break;
            case 'profile-save':
                (new AdminController())->profileSave();
                break;
            case 'skill-list':
                (new AdminController())->skillList();
                break;
            case 'skill-form':
                (new AdminController())->skillForm();
                break;
            case 'skill-save':
                (new AdminController())->skillSave();
                break;
            case 'skill-delete':
                (new AdminController())->skillDelete();
                break;
            case 'social-list':
                (new AdminController())->socialList();
                break;
            case 'social-form':
                (new AdminController())->socialForm();
                break;
            case 'social-save':
                (new AdminController())->socialSave();
                break;
            case 'social-delete':
                (new AdminController())->socialDelete();
                break;
            default:
                (new HomeController())->notFound();
                break;
        }
    }
}
