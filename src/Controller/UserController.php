<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function home()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('user/home.html.twig', compact('users'));
    }

    /**
     * @Route("/user/ajout", name="user.add", methods={"GET", "POST"})
     */
    public function ajouterUser(Request $request)
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if($form->isValid())
            {
                if(!$this->verifMail($user->getMail()))
                {
                    if(!$this->verifTel($user->getTel()))
                    {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->get('session')->getFlashBag()->add('ajout_s', 'User ajouté avec succés !');
                        return $this->redirectToRoute('user');
                    }
                    else
                    {
                        $this->get('session')->getFlashBag()->add('ajout_er_tel', 'Ce numero de telephone existe dèjà !');
                        return $this->redirectToRoute('user.add');
                    }
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('ajout_er_mail', 'Cette adresse email existe dèjà !');
                    return $this->redirectToRoute('user.add');
                }
            }
        }
        return $this->render('user/ajout.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @Route("/user/modif/{tel<[0-9]+>}", name="user.update", methods={"GET", "POST"})
     */
    public function modifierUser(Request $request, $tel)
    {
        if($this->verifTel($tel))
        {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByTel($tel);
            $form = $this->createForm(UserType::class, $user);

            if($request->isMethod('POST'))
            {
                $form->handleRequest($request);

                if($form->isValid())
                {
                    if(!$this->verifMail($user->getMail()))
                    {
                        if(!$this->verifTel($user->getTel()))
                        {
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($user);
                            $entityManager->flush();
                            $this->get('session')->getFlashBag()->add('modif_s', 'User modifié avec succés !');
                            return $this->redirectToRoute('user');
                        }
                        else
                        {
                            $this->get('session')->getFlashBag()->add('modif_er_tel', 'Ce numero de telephone existe dèjà !');
                            return $this->redirectToRoute('user.update', ['tel'=>$tel]);
                        }
                    }
                    else
                    {
                        $this->get('session')->getFlashBag()->add('modif_er_mail', 'Cette adresse email existe dèjà !');
                        return $this->redirectToRoute('user.update', ['tel'=>$tel]);
                    }
                }
            }

            return $this->render('user/modifier.html.twig', ['form'=>$form->createView(), 'user'=>$user]);
        }
        else
        {
            $this->get('session')->getFlashBag()->add('modif_er', 'Vous essayez de modifier un user innexistant !');
            return $this->redirectToRoute('user');
        }
    }

    /**
     * @Route("/user/supprimer/{tel<[0-9]+>}", name="user.delete", methods={"GET","POST"})
     */
    public function suprimerUser(Request $request, $tel)
    {
        if($this->verifTel($tel))
        {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByTel($tel);
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($user);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('sup_s', 'User supprimé !');
            return $this->redirectToRoute('user');
        }
        else
        {
            $this->get('session')->getFlashBag()->add('sup_e', 'Le user que vous essayez de supprimer n\'existe pas !');
            return $this->redirectToRoute('user');
        }
    }

    private function verifMail($mail)
    {
        $trouver = $this->getDoctrine()->getRepository(User::class)->findByMail($mail);
        return $trouver;
    }

    private function verifTel($tel)
    {
        $trouver = $this->getDoctrine()->getRepository(User::class)->findByTel($tel);
        return $trouver;
    }
}
