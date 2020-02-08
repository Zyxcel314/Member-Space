<?php

namespace App\Controller;

use App\Entity\InformationsFamille;
use App\Entity\InformationsMineur;
use App\Form\InformationFamilleType;
use App\Form\InfosMajeurType;
use App\Form\InfosMineurType;
use App\Form\InfosResponsableLegalType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\MembreFamilleType;
use App\Form\RepresentantFamilleType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        return $this->render('back_office/gestionnaire/accueilGestionnaire.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/listeFamilles", name="Gestionnaire.ListeFamilles.show")
     */
    public function showListeFamilles(Environment $twig, RegistryInterface $doctrine)
    {
        $familles = $doctrine->getRepository(RepresentantFamille::class)->findAll();
        return new Response($twig->render('back_office/gestionnaire/listeFamilles.html.twig', [
            'familles' => $familles,
        ]));
    }

    /**
     * @Route("/listeGestionnaires", name="Gestionnaire.ListeGestionnaires.show", methods={"GET"})
     */
    public function showListeGestionnaires(Environment $twig, RegistryInterface $doctrine)
    {
        $gestionnaires = $doctrine->getRepository(Gestionnaires::class)->findAll();
        return new Response($twig->render('back_office/gestionnaire/listeGestionnaires.html.twig', [
            'gestionnaires' => $gestionnaires,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }

    /**
     * @Route("/infosPerso", name="Gestionnaire.InfosPerso.show")
     */
    public function showGestionnaire(Environment $twig, RegistryInterface $doctrine)
    {
        $selectedGestionnaire = $doctrine->getRepository(Gestionnaires::class)->find($this->getUser()->getId());
        return new Response($twig->render('back_office/gestionnaire/showInfosPerso.html.twig', [
            'selected_gestionnaire' => $selectedGestionnaire,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }
}