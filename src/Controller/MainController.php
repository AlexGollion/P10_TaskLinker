<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Entity\Project;

final class MainController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository) {
    }

    #[Route('/', name: 'app_main_home')]
    public function home(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('main/home.html.twig', [
            'projects' => $projects,
        ]);
    }
}
