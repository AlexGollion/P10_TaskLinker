<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employee;
use App\Form\EmployeeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthenticationController extends AbstractController
{
    #[Route('/authentication/registration', name: 'app_authentication_registration')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $employee->setPassword(
                $passwordHasher->hashPassword(
                    $employee,
                    $formData->getPassword()
                )
            );

            $today = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $todayDate = $today->setTime(0, 0);
            $employee->setDate($todayDate);

            $employee->setStatus(EmployeeStatus::CDI);
            
            $manager->persist($employee);
            $manager->flush();
            
            return $this->redirectToRoute('app_main_home');
        }
        return $this->render('authentication/registration.html.twig', [
            'controller_name' => 'AuthenticationController',
        ]);
    }
}
