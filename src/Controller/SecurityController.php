<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use PHPMailer\PHPMailer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/old", name="home")
     */
    public function home() {
        return $this->render('security/home.html.twig');
    }

    /**
     * @Route("/inscription", name="securityRegistration")
     */
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
            return $this->redirectToRoute('securitySendConfirmationEmail', array(
                'email' => $user->getEmail()
            ));
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

        $error = $authenticationUtils->getLastAuthenticationError();

        // dernier email rentrer par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastEmail,
            'error' => $error
        ));
    }

    /**
     * @Route("/checkActivation", name="securityCheckActivation")
     */
    public function checkActivation(Request $request) {
        $check = $request->request->get("check");
        if ($check == 1) {
            return $this->redirectToRoute("home");
        }
        return $this->render("security/check_activation.html.twig");
    }

    /**
     * @Route("/logout", name="securityLogout")
     */
    public function logout() {}

    /**
     * @Route("/emailConfirmation/{email}", name="securitySendConfirmationEmail")
     */
    public function sendConfirmationEmail($email) {

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'odysseeducirquemail@gmail.com';                     // SMTP username
            $mail->Password = 'vivelesclowns';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('odysseeducirquemail@gmail.com', 'PennyWise');
            $mail->addAddress($email);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Activation compte';
            $mail->Body = "<a href='localhost:8000/activationUser/" . $email . "'>Activer votre compte :)</a>";

            $mail->send();

        }
        catch (Exception $e) {}

        return $this->render("security/email.html.twig", array(
            "email" => $email
        ));
    }

    /**
     * @Route("/activationUser/{email}", name="securityActivationUser")
     */
    public function activationUser(ObjectManager $manager, $email) {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);

        $user->setIsActivated(1);

        $manager->persist($user);
        $manager->flush();

        return $this->render('security/activation_message.html.twig', array(
            'user' => $user
        ));
    }
}
