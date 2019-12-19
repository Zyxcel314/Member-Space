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
use App\Entity\InformationsFamille;
use App\Entity\MembreFamille;
use App\Form\RepresentantFamilleType;
use App\Form\InformationFamilleType;

/**
 * @Route("/gestionnaire/superadmin")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class superadminController extends AbstractController
{


        /**
         * @Route("/edit/{id}", name="superadmin.editGestionnaire", methods={"GET"}, requirements={"id"="\d+"})
         */
        public function editGestionnaire(Request $request, Environment $twig, RegistryInterface $doctrine, $id=null)
    {


        $gestionnaire = $doctrine->getRepository(Gestionnaires::class)->findBy([],['id'=>'ASC']);
        return $this->render('gestionnaire/superadminEdit.html.twig');

    }

    /**
     * @Route("/add", name="superadmin.addGestionnaire", methods={"GET"})
     */
    public function addGestionnaire(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        return new Response($twig->render('gestionnaire/superadminAdd.html.twig'));

    }

    /**
     * @Route("/add", name="superadmin.validAddGestionnaire", methods={"POST"})
     */
    public function validAddOrdinateurs(Request $request, Environment $twig, RegistryInterface $doctrine,UserPasswordEncoderInterface $encoder)
    {

        
        $donnees['nomGestionnaire'] = htmlspecialchars($_POST['nomGestionnaire']);

        $donnees['prenomGestionnaire'] = htmlspecialchars($_POST['prenomGestionnaire']);
        $donnees['mdpGestionnaire'] = htmlspecialchars($_POST['mdpGestionnaire']);
        $gestionnaire = new Gestionnaires();
        $hash = $encoder->encodePassword($gestionnaire, $donnees['mdpGestionnaire']);

           // var_dump();

            $gestionnaire = new Gestionnaires();
            $gestionnaire->setNom($donnees['nomGestionnaire'])
                ->setPrenom($donnees['prenomGestionnaire'])
                ->setMotdepasse($hash);


            $doctrine->getEntityManager()->persist($gestionnaire);
            $doctrine->getEntityManager()->flush();


            $this->addFlash('notice', 'Gestionnaire Ajouté !');
            return $this->redirectToRoute('Gestionnaire.showListeGestionnaires');
        }








}


