<?php

namespace App\Controllers;

use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProfileRepository;
use App\Repository\SkillRepository;
use App\Repository\SocialRepository;
use App\Models\Project;
use App\Models\Category;
use App\Models\Profile;

class AdminController extends AbstractController {
    
    // construct pour secure l'admin
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // check si user est log
        if (!isset($_SESSION['user'])) {
            $this->setFlash('error', 'Accès interdit. Veuillez vous connecter.');
            $this->redirect('login');
        }
    }

    // render le dashboard avec les stats
    public function index(): void {
        try {
            $projectRepo = new ProjectRepository();
            $categoryRepo = new CategoryRepository();
            $skillRepo = new SkillRepository();
            $socialRepo = new SocialRepository();
            $profileRepo = new ProfileRepository();

            $this->render('admin/dashboard', [
                'title' => 'Tableau de bord',
                'profile' => $profileRepo->findOne(),
                'stats' => [
                    'projects' => $projectRepo->countAll(),
                    'categories' => $categoryRepo->countAll(),
                    'socials' => $socialRepo->countAll(),
                    'skills' => $skillRepo->countAll()
                ]
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Erreur lors du chargement du tableau de bord.');
            $this->render('admin/dashboard', ['title' => 'Dashboard Admin', 'stats' => []]);
        }
    }

    // list les projects
    public function list(): void {
        $projectRepo = new ProjectRepository();
        $this->render('admin/list', [
            'projects' => $projectRepo->findAll(),
            'title' => 'Liste des Projets'
        ]);
    }

    // form pour add ou edit un project
    public function form(): void {
        $id = $_GET['id'] ?? null;
        $projectRepo = new ProjectRepository();
        $categoryRepo = new CategoryRepository();
        $skillRepo = new SkillRepository();

        $project = $id ? $projectRepo->find((int)$id) : null;
        $categories = $categoryRepo->findAll();
        $skills = $skillRepo->findAll();
        $projectSkills = $id ? $projectRepo->findSkills((int)$id) : [];

        $this->render('admin/form', [
            'project' => $project,
            'categories' => $categories,
            'skills' => $skills,
            'projectSkills' => $projectSkills,
            'title' => $id ? 'Modifier un projet' : 'Ajouter un projet'
        ]);
    }

    // save le project en db
    public function save(): void {
        try {
            $projectRepo = new ProjectRepository();
            $id = $_POST['id'] ?? null;
            
            // creer un new object project
            $project = new Project();
            if ($id) {
                $project->setId((int)$id);
            }
            $project->setTitle($_POST['title'] ?? '');
            $project->setDescription($_POST['description'] ?? '');
            $project->setImage($_POST['image'] ?? '');
            $project->setIdCategory((int)($_POST['id_category'] ?? 0));

            // execute le save
            if ($projectRepo->save($project)) {
                // sync les skills
                $skillIds = $_POST['skills'] ?? [];
                $projectRepo->syncSkills($project->getId(), $skillIds);

                // sync les images de la galerie
                $galleryText = $_POST['gallery_urls'] ?? '';
                $imageUrls = array_filter(array_map('trim', explode("\n", $galleryText)));
                $projectRepo->syncImages($project->getId(), $imageUrls);
                
                $this->setFlash('success', 'Projet enregistré avec succès.');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'enregistrement.');
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('admin-list');
    }

    // delete un project
    public function delete(): void {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $projectRepo = new ProjectRepository();
            if ($projectRepo->delete((int)$id)) {
                $this->setFlash('success', 'Projet supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Impossible de supprimer le projet.');
            }
        }
        $this->redirect('admin-list');
    }

    // list les categories
    public function categoryList(): void {
        $categoryRepo = new CategoryRepository();
        $this->render('admin/category_list', [
            'categories' => $categoryRepo->findAll(),
            'title' => 'Gestion des Catégories'
        ]);
    }

    // form pour la categorie
    public function categoryForm(): void {
        $id = $_GET['id'] ?? null;
        $categoryRepo = new CategoryRepository();
        $category = $id ? $categoryRepo->find((int)$id) : null;

        $this->render('admin/category_form', [
            'category' => $category,
            'title' => $id ? 'Modifier Catégorie' : 'Ajouter Catégorie'
        ]);
    }

    // save la categorie
    public function categorySave(): void {
        try {
            $categoryRepo = new CategoryRepository();
            $id = $_POST['id'] ?? null;
            
            $category = new Category();
            if ($id) {
                $category->setId((int)$id);
            }
            $category->setName($_POST['name'] ?? '');

            if ($categoryRepo->save($category)) {
                $this->setFlash('success', 'Catégorie enregistrée avec succès.');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'enregistrement.');
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('category-list');
    }

    // delete la categorie
    public function categoryDelete(): void {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $categoryRepo = new CategoryRepository();
            if ($categoryRepo->delete((int)$id)) {
                $this->setFlash('success', 'Catégorie supprimée avec succès.');
            } else {
                $this->setFlash('error', 'Impossible de supprimer la catégorie.');
            }
        }
        $this->redirect('category-list');
    }

    // view le form profil
    public function profileForm(): void {
        $profileRepo = new ProfileRepository();
        $profile = $profileRepo->findOne();

        $this->render('admin/profile_form', [
            'profile' => $profile,
            'title' => 'Modifier mon Profil'
        ]);
    }

    // save les infos du profil
    public function profileSave(): void {
        try {
            $profileRepo = new ProfileRepository();
            $imageRepo = new \App\Repository\ImageRepository();
            $id = $_POST['id'] ?? null;
            
            $profile = new Profile();
            if ($id) {
                $profile->setId((int)$id);
            }
            $profile->setName($_POST['name'] ?? '');
            $profile->setDescription($_POST['description'] ?? '');
            $profile->setEmail($_POST['email'] ?? '');
            $profile->setPhoneNumber($_POST['phone_number'] ?? '');
            
            // convert date format
            $dob = $_POST['date_of_birth'] ?? '';
            if ($dob && strpos($dob, '/') !== false) {
                $parts = explode('/', $dob);
                if (count($parts) === 3) {
                    $dob = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
            }
            $profile->setDateOfBirth($dob);

            // save l'image si present
            $imageUrl = $_POST['profile_image'] ?? '';
            if (!empty($imageUrl)) {
                $existingProfile = $profileRepo->findOne();
                $image = null;
                if ($existingProfile && $existingProfile->getIdImage()) {
                    $image = $imageRepo->find($existingProfile->getIdImage());
                }
                
                if (!$image) {
                    $image = new \App\Models\Image();
                }
                
                $image->setUrl($imageUrl);
                $image->setAlt('Photo de profil de ' . $profile->getName());
                $imageRepo->save($image);
                
                $profile->setIdImage($image->getId());
            }

            if ($profileRepo->save($profile)) {
                $this->setFlash('success', 'Profil mis à jour avec succès.');
            } else {
                $this->setFlash('error', 'Erreur lors de la mise à jour.');
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('admin-dashboard');
    }

    // list les skills
    public function skillList(): void {
        $skillRepo = new SkillRepository();
        $this->render('admin/skill_list', [
            'skills' => $skillRepo->findAll(),
            'title' => 'Gestion des Skills'
        ]);
    }

    // form pour le skill
    public function skillForm(): void {
        $id = $_GET['id'] ?? null;
        $skillRepo = new SkillRepository();
        $skill = $id ? $skillRepo->find((int)$id) : null;

        $this->render('admin/skill_form', [
            'skill' => $skill,
            'title' => $id ? 'Modifier un Skill' : 'Ajouter un Skill'
        ]);
    }

    // save le skill
    public function skillSave(): void {
        try {
            $skillRepo = new SkillRepository();
            $id = $_POST['id'] ?? null;
            
            $skill = new \App\Models\Skill();
            if ($id) {
                $skill->setId((int)$id);
            }
            $skill->setName($_POST['name'] ?? '');
            $skill->setLevel((int)($_POST['level'] ?? 0));

            if ($skillRepo->save($skill)) {
                $this->setFlash('success', 'Skill enregistré avec succès.');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'enregistrement.');
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('skill-list');
    }

    // delete un skill
    public function skillDelete(): void {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $skillRepo = new SkillRepository();
            if ($skillRepo->delete((int)$id)) {
                $this->setFlash('success', 'Skill supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Impossible de supprimer la compétence.');
            }
        }
        $this->redirect('skill-list');
    }

    // list les sociales links
    public function socialList(): void {
        $socialRepo = new SocialRepository();
        $this->render('admin/social_list', [
            'socials' => $socialRepo->findAll(),
            'title' => 'Gestion des Réseaux Sociaux'
        ]);
    }

    // form pour social link
    public function socialForm(): void {
        $id = $_GET['id'] ?? null;
        $socialRepo = new SocialRepository();
        $social = $id ? $socialRepo->find((int)$id) : null;

        $this->render('admin/social_form', [
            'social' => $social,
            'title' => $id ? 'Modifier un réseau social' : 'Ajouter un réseau social'
        ]);
    }

    // save le social link
    public function socialSave(): void {
        try {
            $socialRepo = new SocialRepository();
            $id = $_POST['id'] ?? null;
            
            $social = new \App\Models\Social();
            if ($id) {
                $social->setId((int)$id);
            }
            $social->setName($_POST['name'] ?? '');
            $social->setUrl($_POST['url'] ?? '');
            $social->setIcon($_POST['icon'] ?? '');

            if ($socialRepo->save($social)) {
                $this->setFlash('success', 'Réseau social enregistré avec succès.');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'enregistrement.');
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('social-list');
    }

    // delete social link
    public function socialDelete(): void {
        try {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $socialRepo = new SocialRepository();
                if ($socialRepo->delete((int)$id)) {
                    $this->setFlash('success', 'Réseau social supprimé avec succès.');
                } else {
                    $this->setFlash('error', 'Impossible de supprimer le réseau social.');
                }
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue.');
        }
        $this->redirect('social-list');
    }
}
