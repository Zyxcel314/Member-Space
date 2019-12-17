<?php

namespace App\Controller;
use App\Entity\InformationsFamille;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\RepresentantFamilleType;
use App\Form\InformationFamilleType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Environment;

/**
 * @Route("/representant")
 */
class RepresentantFamilleController extends AbstractController
{
    /**
     * @Route("/", name="Representant.accueil", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(RepresentantFamilleRepository $representantFamilleRepository): Response
    {
        return $this->render('representant_famille/espace.html.twig');
    }

    /**
     * @Route("/informationsFamille", name="Representant.informationsFamille", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function informationsFamille(RepresentantFamilleRepository $representantFamilleRepository, Request $request, Environment $twig, RegistryInterface $doctrine): Response
    {
        dump($this->genenererTokenMail());
        $infoFamiliales = $doctrine->getRepository(InformationsFamille::class)->findBy(['representant_famille'=>$this->getUser()],['id'=>'ASC']);
        return $this->render('representant_famille/informationsFamille.html.twig', ['infoFamille' => $infoFamiliales]);
    }

    /**
     * @Route("/informationsFamille/ajouter", name="Representant.informationsFamille.ajouter", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function informationsFamilleAjouter(RepresentantFamilleRepository $representantFamilleRepository, Request $request, Environment $twig, RegistryInterface $doctrine): Response
    {
        $infoFamiliales = new InformationsFamille();
        $form = $this->createForm(InformationFamilleType::class, $infoFamiliales);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $infoFamiliales = $form->getData();
            $infoFamiliales->setRepresentantFamille($this->getUser());
            $infoFamiliales->setDateModification(new \DateTime(date('Y-m-d')));

            $entityManager->persist($infoFamiliales);
            $entityManager->flush();

            return $this->redirectToRoute('Representant.informationsFamille');
        }
        return $this->render('representant_famille/addInformationsFamille.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/informationsFamille/modifier", name="Representant.informationsFamille.modifier", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function informationsFamilleModifier(RepresentantFamilleRepository $representantFamilleRepository, Request $request, Environment $twig, RegistryInterface $doctrine): Response
    {
        $infoFamiliales = $doctrine->getRepository(InformationsFamille::class)->findBy(['representant_famille'=>$this->getUser()],['id'=>'ASC'])[0];
        dump($infoFamiliales);
        $form = $this->createForm(InformationFamilleType::class, $infoFamiliales);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $form->getData() holds the submitted values
            $infoFamiliales = $form->getData();
            $infoFamiliales->setRepresentantFamille($this->getUser());
            $infoFamiliales->setDateModification(new \DateTime(date('Y-m-d')));

            $entityManager->persist($infoFamiliales);
            $entityManager->flush();

            return $this->redirectToRoute('Representant.informationsFamille');
        }
        return $this->render('representant_famille/addInformationsFamille.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/informationsPerso", name="Representant.informationsPerso", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function informationsPerso(RepresentantFamilleRepository $representantFamilleRepository, Request $request, Environment $twig, RegistryInterface $doctrine): Response
    {   $rprstFamille = $doctrine->getRepository(RepresentantFamille::class)->findBy(['id'=>$this->getUser()]);
        return $this->render('representant_famille/infoPerso.html.twig', ['representantFamille' => $rprstFamille]);
    }

    /**
     * @Route("/informationsMembreFamille", name="Representant.informationsMembreFamille", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function membreFamille(RepresentantFamilleRepository $representantFamilleRepository, Request $request, Environment $twig, RegistryInterface $doctrine): Response
    {   $membreFamille = $doctrine->getRepository(MembreFamille::class)->findBy([],['id'=>'ASC']);
        return $this->render('representant_famille/membreFamille.html.twig', ['membreFamille' => $membreFamille]);
    }


    /**
     * @Route("/inscription", name="Representant.ajouter", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $representantFamille = new RepresentantFamille();
        $token = $this->genenererTokenMail();
        $representantFamille->setEstActive(0);
        $representantFamille->setMailTokenVerification($token);
        $form = $this->createForm(RepresentantFamilleType::class, $representantFamille)
            ->add('confirmermdp', PasswordType::class,['label' => 'Confirmez', "mapped"=>false])
            ->add('save', SubmitType::class, ['label' => 'Créer un compte']);
        $form->handleRequest($request);
        dump($form->get('motdepasse')->getData());
        dump($form->get('confirmermdp')->getData());
        $passwordsmatch = strcmp($form->get("motdepasse")->getData(),$form->get("confirmermdp")->getData())==0;
        dump($passwordsmatch);
        if(!$passwordsmatch){
            $form->get('confirmermdp')->addError(new FormError('Les deux mot de passes ne correspondent pas'));
        }

        if ($form->isSubmitted() && $form->isValid() && $passwordsmatch) {

            $hash = $encoder->encodePassword($representantFamille, $representantFamille->getMotdepasse());
            $representantFamille->setMotdepasse($hash);
            //$representantFamille->setDateFinAdhesion(new \DateTime('2019-01-01'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($representantFamille);
            $entityManager->flush();

            $this->sendConfirmationEmail($form->get('mail')->getData(),$token);

            return $this->render('representant_famille/confirmation.html.twig', [
                'mail' => $form->get('mail')->getData()
            ]);

        }

        return $this->render('representant_famille/new.html.twig', [
            'representant_famille' => $representantFamille,
            'form' => $form->createView(),
        ]);
    }

    public function sendConfirmationEmail($email,$token) {

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
            $mail->setFrom('odysseeducirquemail@gmail.com', 'Activation Odyssee du cirque');
            $mail->addAddress($email);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Activation compte';
            $mail->Body = "<br><a href='localhost:8000/representant/activer/" . $token . "'>Cliquez ici</a>sur le lien pour activer votre compte http://localhost:8000/representant/activer/".$token ;

            $mail->send();

        }
        catch (Exception $e) {}
    }

    /**
     * @Route("/{id}", name="Representant.afficher", methods={"GET"})
     */
    public function show(RepresentantFamille $representantFamille): Response
    {
        return $this->render('representant_famille/show.html.twig', [
            'representant_famille' => $representantFamille,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="representant_famille_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RepresentantFamille $representantFamille): Response
    {
        $form = $this->createForm(RepresentantFamilleType::class, $representantFamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Representant.afficher');
        }

        return $this->render('representant_famille/edit.html.twig', [
            'representant_famille' => $representantFamille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Representant.supprimer", methods={"DELETE"})
     */
    public function delete(Request $request, RepresentantFamille $representantFamille): Response
    {
        if ($this->isCsrfTokenValid('delete'.$representantFamille->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($representantFamille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('representant_famille_index');
    }

    /**
     * @Route("/activer/{token}", name="Representant.activer")
     */
    public function activationUser(ObjectManager $manager, $token) {
        $representant = $this->getDoctrine()->getManager()->getRepository(RepresentantFamille::class)->findOneBy(['mailTokenVerification' => $token]);

        $representant->setEstActive(1);

        $manager->persist($representant);
        $manager->flush();

        return $this->render('representant_famille/activation.html.twig', array(
            'representant' => $representant
        ));
    }

    public function genenererTokenMail(){
        $token = "0123456789ABCDEF0123456789ABCDEF";
        $token = str_shuffle($token);
        $token = substr($token,strlen($token)/2);
        //$token = substr($token,-18);
        return $token;
    }

}
