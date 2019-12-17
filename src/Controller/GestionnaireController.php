<?php

namespace App\Controller;

use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\RepresentantFamille;
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
 * @Route("/gestionnaire")
 * @IsGranted("ROLE_ADMIN")
 */
class GestionnaireController extends AbstractController
{
    /**
     * @Route("/", name="Gestionnaire.accueil", methods={"GET"})
     */
    public function index()
    {
        return $this->render('gestionnaire/espace.html.twig');
    }

    /**
     * @Route("/listeGestionnaires", name="Gestionnaire.showListeGestionnaires", methods={"GET"})
     */
    public function showListeGestionnaires(Environment $twig, RegistryInterface $doctrine)
    {
        $gestionnaires = $doctrine->getRepository(Gestionnaires::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/listeGestionnaires.html.twig', ['gestionnaires' => $gestionnaires]));
    }

    /**
     * @Route("/listeFamilles", name="Gestionnaire.showListeFamilles", methods={"GET"})
     */
    public function showListeFamilles( Environment $twig, RegistryInterface $doctrine)
    {
        $familles = $doctrine->getRepository(RepresentantFamille::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/listeFamilles.html.twig', ['familles' => $familles]));
    }

    /**
     * @Route("/droits", name="Gestionnaire.showDroits", methods={"GET"})
     */
    public function showDroits(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $droits = $doctrine->getRepository(Droits::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/droits.html.twig', ['droits' => $droits]));
    }
}


