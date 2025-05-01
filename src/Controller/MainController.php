<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Entity\Project;
use Symfony\Bundle\SecurityBundle\Security;

#[IsGranted('ROLE_USER')]
final class MainController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository) {
    }

    #[Route('/', name: 'app_main_dispatch')]
    public function dispatch(): Response
    {
        return $this->render('security/dispatch.html.twig');
    }

    #[Route('/home', name: 'app_main_home')]
    public function home(Security $security): Response
    {
        $user = $security->getUser();

        if ($security->isGranted('ROLE_ADMIN')) {
            $projects = $this->projectRepository->findAll();
        }
        else {
            $projects = $this->projectRepository->findProjectsByUser($user->getId());
        }

        return $this->render('main/home.html.twig', [
            'projects' => $projects,
        ]);
    }
}
