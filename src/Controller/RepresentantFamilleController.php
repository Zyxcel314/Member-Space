<?php

namespace App\Controller;
use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\InformationMajeur;
use App\Entity\InformationsFamille;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\RepresentantFamilleType;
use App\Form\InformationFamilleType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Environment;

class RepresentantFamilleController extends AbstractController
{
    /**
     * @Route("/representant", name="Representant.accueil", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(RepresentantFamilleRepository $representantFamilleRepository): Response
    {
        return $this->render('front_office/representant_famille/accueilRepresentant.html.twig');
    }

    /**
     * @Route("/representant/informationsPerso", name="Representant.InfosPerso.show")
     * @IsGranted("ROLE_USER")
     */
    public function showInformationsPersoUSER(RegistryInterface $doctrine)
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->findOneBy(['id' => $this->getUser()->getId()]);
        return $this->render('front_office/representant_famille/showInfosPerso.html.twig', [
            'representant' => $representant
        ]);
    }

    /**
     * @Route("/representant/informationsPerso/edit", name="Representant.InfosPerso.edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editInformationsPersoUSER(Request $request, RegistryInterface $doctrine)
    {
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $form = $this->createForm(RepresentantFamilleType::class, $representantFamille);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('succes', 'Informations personnelles modifiées ! ');

            return $this->redirectToRoute('Representant.InfosPerso.show');
        }

        return $this->render('front_office/representant_famille/editRepresentant.html.twig', [
            'representant_famille' => $representantFamille,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/representant/inscription", name="Representant.ajouter", methods={"GET","POST"})
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
        dump($form->get('motDePasse')->getData());
        dump($form->get('confirmermdp')->getData());
        $passwordsmatch = strcmp($form->get("motDePasse")->getData(),$form->get("confirmermdp")->getData())==0;
        dump($passwordsmatch);
        if(!$passwordsmatch){
            $form->get('confirmermdp')->addError(new FormError('Les deux mots de passes ne correspondent pas'));
        }
        $dateNaissance = $representantFamille->getDateNaissance();
        $dateActuelle = new \DateTime(date('Y-m-d'));
        $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
        $majeur = true;
        if ( $dateNaissance > $dateMajorite ) {
            $form->get('dateNaissance')->addError(new FormError('Il faut être majeur pour s\'insrire'));
            $majeur = false;
        }
        if ($form->isSubmitted() && $form->isValid() && $passwordsmatch && $majeur)
        {
            $hash = $encoder->encodePassword($representantFamille, $representantFamille->getMotdepasse());
            $representantFamille->setMotdepasse($hash);
            $representantFamille->setDateFinAdhesion(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $membre = new MembreFamille();
            $membre
                ->setNom($representantFamille->getNom())
                ->setPrenom($representantFamille->getPrenom())
                ->setDateNaissance($representantFamille->getDateNaissance())
                ->setCategorie('Majeur')
                ->setNoClient($representantFamille->getId() . 'RP')
                ->setTraitementDonnees(0)
                ->setDateMAJ($dateActuelle)
                ->setRepresentantFamille($representantFamille)
                ->setReglementActivite(0);
            $entityManager->persist($representantFamille);
            $entityManager->persist($membre);
            $entityManager->flush();

            $this->sendConfirmationEmail($form->get('mail')->getData(),$token);

            return $this->render('front_office/representant_famille/confirmation.html.twig', [
                'mail' => $form->get('mail')->getData()
            ]);

        }

        return $this->render('front_office/representant_famille/newRepresentant.html.twig', [
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
     * @Route("/representant/activer/{token}", name="Representant.activer")
     */
    public function activationUser(ObjectManager $manager, $token) {
        $representant = $this->getDoctrine()->getManager()->getRepository(RepresentantFamille::class)->findOneBy(['mailTokenVerification' => $token]);

        $representant->setEstActive(1);

        $dateMAJ = new \DateTime();

        $membre = new MembreFamille();
        $membre
            ->setNoClient(0)
            ->setCategorie('Majeur')
            ->setNom($representant->getNom())
            ->setPrenom($representant->getPrenom())
            ->setDateNaissance($representant->getDateNaissance())
            ->setTraitementDonnees(0)
            ->setDateMAJ($dateMAJ)
            ->setRepresentantFamille($representant)
            ->setReglementActivite(0);

        $info_majeur = new InformationMajeur();
        $info_majeur->setMail($representant->getMail());
        $info_majeur->setCommunicationResponsableLegal(0);
        $membre->setInformationMajeur($info_majeur);

        $manager->persist($membre);
        $manager->persist($representant);
        $manager->flush();

        return $this->render('front_office/representant_famille/activation.html.twig', array(
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

    public function droitNecessaire($listeDroits, String $codeDroit)
    {
        for ( $i=0; $i<count($listeDroits); $i++ )
        {
            if ( $listeDroits[$i]->getCode() == $codeDroit ) { return $listeDroits[$i]->getLibelle(); }
        }
        return "ERREUR : Fonction droitNecessaire()";
    }

    public function hasDroits($dispositions, String $codeDroit)
    {
        for ( $i=0; $i<count($dispositions); $i++ )
        {
            if ( $dispositions[$i]->getDroits()->getCode() == $codeDroit ) { return true; }
        }
        return false;
    }

    /**
     * @Route("/gestionnaire/representant/{id}/informationsPerso", name="Gestionnaire.Representant.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showRepresentantGEST(RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "RP_VOIR");

        if ( $this->hasDroits($dispositions, "RP_VOIR") )
        {
            $representantArray = $doctrine->getRepository(RepresentantFamille::class)->findBy(['id' => $representantFamille->getId()]);
            $representant = $doctrine->getRepository(RepresentantFamille::class)->find($representantFamille->getId());
            return $this->render('back_office/representant_famille/showRepresentant.html.twig', [
                'representant' => $representant,
                'representantArray' => $representantArray,
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/ajouterRepresentant", name="Gestionnaire.Representant.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addRepresentantGEST(RegistryInterface $doctrine, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "RP_AJOUT");

        if ( $this->hasDroits($dispositions, "RP_AJOUT") )
        {
            $representantFamille = new RepresentantFamille();
            $form = $this->createForm(RepresentantFamilleType::class, $representantFamille)
                ->add('dateFinAdhesion', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true
                ])
                ->add('estActive');

            $form->handleRequest($request);

            $passwordsmatch = strcmp($form->get("motDePasse")->getData(), $form->get("confirmermdp")->getData()) == 0;
            dump($passwordsmatch);
            if (!$passwordsmatch) {
                $form->get('confirmermdp')->addError(new FormError('Les deux mots de passes ne correspondent pas'));
            }
            $dateNaissance = $representantFamille->getDateNaissance();
            $dateActuelle = new \DateTime(date('Y-m-d'));
            $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
            $majeur = true;
            if ($dateNaissance > $dateMajorite) {
                $form->get('dateNaissance')->addError(new FormError('Il faut que le représentant soit majeur pour l\'ajouter dans la base de données'));
                $majeur = false;
            }
            if ($form->isSubmitted() && $form->isValid() && $passwordsmatch && $majeur) {
                $token = $this->genenererTokenMail();
                $representantFamille->setMailTokenVerification($token);
                $hash = $encoder->encodePassword($representantFamille, $representantFamille->getMotdepasse());
                $representantFamille->setMotdepasse($hash);
                $dateActuelle = new \DateTime();
                $membre = new MembreFamille();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($representantFamille);
                $membre
                    ->setNom($representantFamille->getNom())
                    ->setPrenom($representantFamille->getPrenom())
                    ->setDateNaissance($representantFamille->getDateNaissance())
                    ->setCategorie('Majeur')
                    ->setNoClient($representantFamille->getId() . 'RP')
                    ->setTraitementDonnees(0)
                    ->setDateMAJ($dateActuelle)
                    ->setRepresentantFamille($representantFamille)
                    ->setReglementActivite(0);
                $entityManager->persist($membre);
                $entityManager->flush();
                $this->addFlash('succes', 'Représentant de famille ajouté !');

                return $this->redirectToRoute('Gestionnaire.ListeFamilles.show');
            }

            return $this->render('back_office/representant_famille/addRepresentant.html.twig', [
                'representant_famille' => $representantFamille,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/modifierRepresentant", name="Gestionnaire.Representant.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editRepresentantGEST(RegistryInterface $doctrine, Request $request, RepresentantFamille $representantFamille): Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "RP_MODIF");

        if ( $this->hasDroits($dispositions, "RP_MODIF") )
        {
            $form = $this->createForm(RepresentantFamilleType::class, $representantFamille)
                ->add('dateFinAdhesion', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true
                ])
                ->add('estActive');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('succes', 'Représentant de famille modifié !');

                return $this->redirectToRoute('Gestionnaire.ListeFamilles.show');
            }

            return $this->render('back_office/representant_famille/editRepresentant.html.twig', [
                'representant_famille' => $representantFamille,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/supprimerRepresentant", name="Gestionnaire.Representant.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteRepresentantGEST(RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        /*if ( !$this->isCsrfTokenValid('representant_delete', $request->get('token')) ) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }*/
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "RP_SUPPR");

        if ( $this->hasDroits($dispositions, "RP_SUPPR") )
        {
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
            $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
            try {
                if ($membreFamille != null) {
                    $doctrine->getEntityManager()->remove($membreFamille);
                }
                if ($infosFamille != null) {
                    $doctrine->getEntityManager()->remove($infosFamille);
                }
                $doctrine->getEntityManager()->remove($representantFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Représentant supprimé !');

            return $this->redirectToRoute('Gestionnaire.ListeFamilles.show');
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}
