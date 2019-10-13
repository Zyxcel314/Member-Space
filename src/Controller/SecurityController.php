<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="securityRegistration")
)     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        // Formulaire lié au champs de User
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('securityLogin');
        }
        return $this->render(
            'security/registration.html.twig', [
                'form'=>$form->createView()
            ]
            );
    }

    /**
     * @Route("/login", name="securityLogin")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) {
        // récupère les erreurs
        $error = $authenticationUtils->getLastAuthenticationError();

        // dernier email rentrer par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastEmail,
            'error'         => $error,
        ));
    }
}
