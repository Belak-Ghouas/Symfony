<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateurs;
use App\Form\RegistrationType;


class SecurityController extends AbstractController
{
/**
 * 
 * @Route("/Inscription", name="security_registration")
 */
  public function Inscription(Request $request , ObjectManager $manager ){
        $user=new Utilisateurs();
        $form= $this->createForm(RegistrationType::class,$user);


        $form->handleRequest($request);
        return $this->render('security/Registration.html.twig',[
            'form'=>$form->createView()
        ]);
        if ($form->isSubmitted() && $form->isValid() ) {
                $manager.persist($user);
                $manager.flush();
        return $this->render('security/Registration.html.twig',[
            'form'=>$form->createView()
        ]);

        }
    }
}
