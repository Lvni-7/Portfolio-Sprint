<?php

namespace App\Controllers;

use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProfileRepository;
use App\Repository\SkillRepository;

class HomeController extends AbstractController {
    
    // render la home page publique
    public function index(): void {
        try {
            // init repos
            $projectRepo = new ProjectRepository();
            $categoryRepo = new CategoryRepository();
            $profileRepo = new ProfileRepository();
            $skillRepo = new SkillRepository();
            $socialRepo = new \App\Repository\SocialRepository();

            // fetch all data pour le frontend
            $projects = $projectRepo->findAll();
            $categories = $categoryRepo->findAll();
            $skills = $skillRepo->findAll();
            $profile = $profileRepo->findOne();
            $socials = $socialRepo->findAll();

            // render la view avec les data
            $this->render('home', [
                'projects' => $projects,
                'categories' => $categories,
                'skills' => $skills,
                'socials' => $socials,
                'profile' => $profile,
                'selectedCategory' => null,
                'title' => "Léni Mette | Portfolio"
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            // fallback si error
            $this->render('home', [
                'projects' => [],
                'categories' => [],
                'skills' => [],
                'socials' => [],
                'profile' => null,
                'selectedCategory' => null,
                'title' => "Léni Mette | Portfolio"
            ]);
        }
    }

    // handle error 404
    public function notFound(): void {
        http_response_code(404);
        $this->render('404', [
            'title' => 'Page Introuvable'
        ]);
    }
}
