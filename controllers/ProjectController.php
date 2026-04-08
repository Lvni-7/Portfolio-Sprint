<?php

namespace App\Controllers;

use App\Repository\ProjectRepository;

class ProjectController extends AbstractController {
    
    // show les details d'un project
    public function show(): void {
        try {
            // get l'id de l'url
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $this->redirect('home');
                return;
            }

            // fetch le project en db
            $projectRepo = new ProjectRepository();
            $project = $projectRepo->find((int)$id);

            // check si project existe
            if (!$project) {
                (new HomeController())->notFound();
                return;
            }

            // render la view details
            $this->render('project_details', [
                'project' => $project,
                'title' => $project->getTitle() . ' - Détails'
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->redirect('home');
        }
    }
}
