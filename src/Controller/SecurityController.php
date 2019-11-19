<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
/**
 * 
 * @Route("/Inscription", name="security_registration")
 */
  public function Inscription(Request $request , ObjectManager $manager ,UserPasswordEncoderInterface $encoder ){
        $user=new Utilisateurs();
        $form= $this->createForm(RegistrationType::class,$user);


        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid() ) {

            $file = $form['image']->getData();
          if ($file) {

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); 
          
             
             $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
          try {
            $file->move(
                $this->getParameter('image_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }
                $hash=$encoder->encodePassword($user,$user->getPassword());
                 $user->setPassword($hash);
                 $user->setImage($newFilename);
                $manager->persist($user);
                $manager->flush();

        return $this->redirectToRoute('secure_login');

        }

        return $this->render('security/Registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }

/**
 * @Route("/login" , name="secure_login")
 * 
 */
    public function login() {

        return $this->render('security/login.html.twig');
    }
}
