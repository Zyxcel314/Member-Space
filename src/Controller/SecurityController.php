<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="securityRegistration")
)     */
    public function registration() {
        $user = new User();
        // Formulaire lié au champs de User
        $form = $this->createForm(RegistrationType::class, $user);
        return $this->render(
            'security/registration.html.twig', [
                'form'=>$form->createView()
            ]
            );
    }
}
