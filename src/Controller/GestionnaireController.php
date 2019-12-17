<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;


class GestionnaireController extends AbstractController
{



    /**
     * @Route("/gestionnaire/showUsers", name="gestionnaire.showUsers",methods={"GET"})
     */
    public function showUsers(Request $request, Environment $twig, RegistryInterface $doctrine){


        $users = $doctrine->getRepository(User::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/showUsers.html.twig', ['users' => $users]));


    }



    /**
     * @Route("/gestionnaire/addUsers", name="Gestsionnaire.addUsers", methods={"GET"})
     */
    public function addUsers(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $users = $doctrine->getRepository(User::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/addUsers.html.twig', ['users' => $users]));

    }

    /**
     * @Route("/gestionnaire/{id}/deleteUsers", name="Gestionnaire.deleteUsers", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function deleteUsers(Request $request, Environment $twig, RegistryInterface $doctrine,$id)
    {



        $users = $doctrine->getRepository(User::class)->find($id);
        $doctrine->getEntityManager()->remove($users);
        $doctrine->getEntityManager()->flush();


        $this->addFlash('notice', 'Utilisateur ajoutée');
        return $this->redirectToRoute('gestionnaire.showUsers');



    }


    /*private function validDonnees($donnees)
    {
        $erreurs=array();
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['typeMachine'])))
            $erreurs['typeMachine']='Le nom doit-être composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomMachine'])))
            $erreurs['nomMachine']='Le nom doit-être composé de 2 lettres minimum';
        if(! is_numeric($donnees['ram']))
            $erreurs['ram']='Veuillez saisir une valeur numérique';


        if(!preg_match("#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#", $donnees["date_achat"], $matches)){
            $erreurs["date_achat"] = "La date doit être au format JJ/MM/AAAA";}
        else {
            if(!checkdate($matches[2], $matches[1], $matches[3])){
                $erreurs["date_achat"] = "La date n'est pas valide.";}
            else {
                $donnees["date_achat"]=$matches[3]."-".$matches[2]."-".$matches[1];}}
        return $erreurs;
    }*/


    /**
     * @Route("/gestionnaire/{id}/editUsers", name="Gestionnaire.editUsers", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function editUsers(Request $request, Environment $twig, RegistryInterface $doctrine, $id=null)
    {


        $users = $doctrine->getRepository(User::class)->find($id);
        return $this->render('gestionnaire/editUsers.html.twig', ['donnees'=>$users]);



    }

    /**
     * @Route("/gestionnaire/validAddUsers", name="Gestionaire.validUsers", methods={"POST"})
     */
   public function validAddUsers(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

      /*  if(!$this->isCsrfTokenValid('form_produit',$request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token');
        }
*/
        $donnees['id']=$request->request->get('id');
        $donnees['email']=htmlspecialchars($_POST['email']);
        $donnees['username']=htmlspecialchars($_POST['username']);
        $donnees['password']=htmlspecialchars($_POST['password']);
      /*
        return $this->render('ordinateur_v1/Ordinateur_add.html.twig', ['donnees'=>$donnees,'salles' => $salles]);

        if (!empty($erreurs)) {
            $ordinateur = $doctrine->getRepository(Salle::class)->findAll();
            return $this->render('ordinateur_v1/Ordinateur_add.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'ordinateur' => $ordinateur]);
        } else {
*/
            $users = new User();
            $users->setEmail($donnees['email'])
                ->setUsername($donnees['username'])
                ->setPassword($donnees['password']);


            $doctrine->getEntityManager()->persist($users);
            $doctrine->getEntityManager()->flush();


            $this->addFlash('notice', 'Utilisateur ajouté !');
            return $this->redirectToRoute('gestionnaire.showUsers');
        }



    /**
     * @Route("/gestionnaire/validEditUsers", name="Gestionnaire.validFormEditUsers", methods={"POST"})
     */


    public function validFormEditUsers(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
       /* if(!$this->isCsrfTokenValid('form_produit',$request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token');
        }
       */
        $donnees['id']=$request->request->get('id');
        $donnees['email']=htmlspecialchars($_POST['email']);
        $donnees['username']=htmlspecialchars($_POST['username']);
        $donnees['password']=htmlspecialchars($_POST['password']);

        $users = $doctrine->getRepository(User::class)->find($donnees['id']);
        $users->setEmail($donnees['email'])
            ->setUsername($donnees['username'])
            ->setPassword($donnees['password']);


        $doctrine->getEntityManager()->persist($users);
        $doctrine->getEntityManager()->flush();

        $this->addFlash('notice', 'Utilisateur modifié ! ');
        return $this->redirectToRoute('gestionnaire.showUsers');




    }


}


