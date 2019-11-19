<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateurs;
use App\Entity\Publications;
use App\Entity\Video;
use App\Entity\Commentaire;
use App\Entity\Messages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MyController extends AbstractController
{
    


     /**
     * @Route("/creerUtilisateur", name="creerUtilisateur")
     */
    public function createAction (Request $request) {

      
        $utilisateur=new Utilisateurs();  //crée l"objet
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $utilisateur);

        $formBuilder    //creation du formulaire avec les champs qui suis 
        ->add('nom',TextType::class, array('required'=> true)     )
        ->add('prenom',TextType::class, array('required'=> true))
        ->add('email',EmailType::class, array('required'=> true))
        ->add('password',PasswordType::class)
        ->add('image',fileType::class, array('required'=> true ,'constraints' => [
          new File([
              'mimeTypes' => [
                  'image/jpg',
                  'image/png',
              ],
              'mimeTypesMessage' => 'Please upload a valid PDF document',
          ])
      ], 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-top:15px')))
        ->add('Enregistrer', SubmitType::class, array('label' => 'Enregistrer'))
        ;

        $form = $formBuilder->getForm();
        
        
        $form->handleRequest($request);
       
        if ($form->isSubmitted() /*&& $form->isValid()*/ ) {
        $utilisateur=$form->getData();
         
         /* $this->init();
          $listUtil = $this->repositoryUtil->findBy( array('nom' => $form->get('nom')->getData() ));
        
          if (count($listUtil)>=1) {
            return $this->render('inscription.html.twig', array('form' => $form->createView(),'ErreurInscription'=>$this->ErreurInscription));
        
          }
        */
          $utilisateur->setNom($form->get('nom')->getData());
          $utilisateur->setPrenom($form->get('prenom')->getData());
          $utilisateur->setEmail($form->get('email')->getData());
          $utilisateur->setPassword (md5($form->get('password')->getData()));
        
          
      
          $file = $form['image']->getData();
          if ($file) {
          $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);  
          $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
          try {
            $file->move(
                $this->getParameter('upload_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }


          /*$nomdufichier=$file->getClientOriginalName();
          $extention=explode(".",$nomdufichier);
        
          if(($extention[count($extention)-1]=="jpg")||
           ($extention[count($extention)-1]=="JPG")||
           ($extention[count($extention)-1]=="jpeg")||
           ($extention[count($extention)-1]=="JPEG")||
           ($extention[count($extention)-1]=="PNG")||
           ($extention[count($extention)-1]=="png")){
        */
           /*$destination='Upload/Images/'.$nomdufichier;*/
         $utilisateur->setImage($newFilename);
          }
         $em = $this->getDoctrine()->getManager();
                   $em->persist($utilisateur); // prépare l'insertion dans la BD
                   $em->flush(); // insère dans la BD
                  // move_uploaded_file($file,$destination);
        
                   return $this->render('index.html.twig');
        }else{
          return $this->render('inscription.html.twig', array('form' => $form->createView()));
        }
           
               }
        
        



/**
* @Route("/about", name="about")
*
*/
public function aboutAction (Request $request){
   
  return $this->render('about.html.twig');
  
  }


  /**
     * @Route("/", name="homepage")
     */
 public function indexAction (Request $request) {
  $this->session = $request->getSession();
  if ($this->session->has('id') ){
    $this->init();

    $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
    $commentaire=$this->repositoryCom->findAll();
    $utilisateurs =$this->repositoryUtil->findAll();
    $messages =$this->repositoryMes->findAll();

    return $this->render('accueil.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));
  }else{
    return $this->render('index.html.twig');
  }

}


    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexionAction (Request $request){


      if($request->getMethod() == 'POST')
      {

       $nom =$_POST['username'];
       $password=md5($_POST['password']);


       $this->init();
       $user=$this->repositoryUtil->findOneBy(array('nom' => $nom , 'password'=>$password));
       $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
       

       if(count($user)==1){

          //quand quelqun se connecte
         $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
         $commentaire=$this->repositoryCom->findAll();
         $commentaire=$this->repositoryCom->findAll();
         $messages =$this->repositoryMes->findAll();

         $this->session = $request->getSession();
         $this->session->set('id',$user->getId()); 
         $this->session->set('user',$user->getNom()); 
         $this->session->set('profile',$user->getImage()); 


         return $this->render('accueil.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));

       }else

       return $this->render('index.html.twig',array('ErreurLogin'=>$this->ErreurLogin));

     }


   }



    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction (Request $request){
     $this->session = $request->getSession();
     $this->session->clear();

     return $this->render('index.html.twig');


   }

   /**
     * @Route("/test", name="test")
     */
    public function testAction (Request $request){
      
     $util = new Utilisateurs();  
     $util->setNom('hello');
     $util->setPrenom('abdel');
     $util->setPassword('12421');
     $util->setEmail('hghg@fgfg.fr');
     $util->setImage('3232');

     $em = $this->getDoctrine()->getManager();
     $em->persist($util); // prépare l'insertion dans la BD
     $em->flush(); // insère dans la BD
     return $this->render('index.html.twig');
        }

   public function init (){
    
    $this->repositoryUtil = $this->getDoctrine()->getRepository('Utilisateurs::class');
    $this->repositoryCom = $this->getDoctrine()->getRepository('Commentaire');
    $this->repositoryPub=$this->getDoctrine()->getRepository('Publications');
    $this->repositoryMes=$this->getDoctrine()->getRepository('Messages');

  }






    /**
     * @Route("/home", name="home")
     */
    public function homeAction (Request $request){
      $this->session = $request->getSession();
      if ($this->session->has('id') ){
        $this->init();

        $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
        $commentaire=$this->repositoryCom->findAll();
        $messages =$this->repositoryMes->findAll();

        return $this->render('accueil.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));
      }else{
        return $this->render('index.html.twig');
      }
    }


    /**
     * @Route("/poste", name="poste")
     */
    public function posteAction (Request $request){
      $this->session = $request->getSession();

      if($request->getMethod() == 'POST') {

        if ($this->session->has('id') ){
          $this->init();
          

          $now=date("Y-m-d H:i:s"); 
          $date =  new \DateTime($now);

          $publication = new Publications();
          $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
          $commentaire=$this->repositoryCom->findAll();
          $messages =$this->repositoryMes->findAll();
            $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
          if($_POST['nom']){
            $nom =trim($_POST['nom']);
            $publication->setNom($nom);
          }else{
            $publication->setNom("");
          }

          $publication->setUser($user);
          $publication->setDate($date);

          if ($_FILES['file']['name']){

            $file =$_FILES['file']['tmp_name'];
            $nomdufichier=$_FILES['file']['name'];
            $extention=explode(".",$nomdufichier);

            if(($extention[count($extention)-1]=="jpg")||
             ($extention[count($extention)-1]=="JPG")||
             ($extention[count($extention)-1]=="jpeg")||
             ($extention[count($extention)-1]=="JPEG")||
             ($extention[count($extention)-1]=="PNG")||
             ($extention[count($extention)-1]=="png")){

              $destination='Upload/Images/'.$nomdufichier;
            $publication->setChemin($destination);
            $publication->setType("photo");

          }else if(($extention[count($extention)-1]=="MP4")||
            ($extention[count($extention)-1]=="mp4")||
            ($extention[count($extention)-1]=="AVI")||
            ($extention[count($extention)-1]=="avi")||
            ($extention[count($extention)-1]=="WMV")||
            ($extention[count($extention)-1]=="wmv")||
            ($extention[count($extention)-1]=="3gp")||
            ($extention[count($extention)-1]=="3GP")){

           $destination='Upload/Videos/'.$nomdufichier;
           $publication->setChemin($destination);
           $publication->setType("video");
         }else if (($extention[count($extention)-1]=="PDF")||
          ($extention[count($extention)-1]=="pdf")||
          ($extention[count($extention)-1]=="doc")||
          ($extention[count($extention)-1]=="DOC")||
          ($extention[count($extention)-1]=="docx")||
          ($extention[count($extention)-1]=="DOCX") ) {

          $destination='Upload/Documents/'.$nomdufichier;
          $publication->setChemin($destination);
          $publication->setType("document");
        }else{

          return $this->render('accueil.html.twig',array('ErreurPub'=>$this->ErreurPub,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$publication,'commentaire'=>$commentaire,'messages'=>$messages));
        }

        move_uploaded_file($file,$destination);
      }else{
            if ($_POST['nom']!="") {
              $publication->setType("article");
            }else return $this->render('accueil.html.twig',array('ErreurPub'=>$this->ErreurPub,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));
        
      }
      
      $em = $this->getDoctrine()->getManager();
           $em->persist($publication); // prépare l'insertion dans la BD
           $em->flush(); // insère dans la BD
           
           $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));

           return $this->render('accueil.html.twig',array('ErreurPub'=>$this->ErreurPub,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));

         }


       }
     }




 /**
     * @Route("/comment", name="comment")
     */
 public function commentAction (Request $request){
  $this->session = $request->getSession();


  if ($this->session->has('id') ){
    $now=date("Y-m-d H:i:s"); 
    $date =  new \DateTime($now);

    $this->init();


    $comment = new Commentaire();
    $comment->setComment(trim($_POST['comment']));
    $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
    $pub=$this->repositoryPub->findOneBy(array('id' =>$_POST['idPub']));
    $pub->setNbrcomment($pub->getNbrcomment()+1);
    $comment->setUser($user);
    $comment->setPublications($pub);
    $comment->setDate($date);
    $em = $this->getDoctrine()->getManager();
           $em->persist($comment); // prépare l'insertion dans la BD
           $em->flush(); // insère dans la BD

           $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document')),array('date' => 'DESC'));
           $commentaire=$this->repositoryCom->findAll();
           $messages =$this->repositoryMes->findAll();

           return $this->render('accueil.html.twig',array('ErreurPub'=>$this->ErreurPub,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));


         }else{
          return $this->render('index.html.twig');
        }
      }


/**
     * @Route("/publication", name="publication")
     */
public function publicationAction (Request $request){
  $this->session = $request->getSession();

  if ($this->session->has('id') ){
   $this->init();
   $type=$_GET['type'];
   $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
   $Pub=$this->repositoryPub->findBy(array('type'=> $type , 'user'=>$user),array('date' => 'DESC'));

   $commentaire=$this->repositoryCom->findAll();
   return $this->render('publications.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire));

 }else{
  return $this->render('index.html.twig');
}
}

    /**
     * @Route("/supprimer", name="supprimer")
     */
    public function supprimerAction (Request $request){
      $this->session = $request->getSession();

      if ($this->session->has('id')){
       $this->init();
       $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
       $Pub=$this->repositoryPub->findBy(array('id'=> $_GET['id']));
        if ($user==$Pub[0]->getUser()) { //vérifier que la publication appartient a l'utilisateur
        if ($Pub[0]->getType()=="photo" || $Pub[0]->getType()=="video" ||$Pub[0]->getType()=="document") {

          unlink($Pub[0]->getChemin());
          
        }
        $type=$Pub[0]->getType();

        $em = $this->getDoctrine()->getManager();
           $em->remove($Pub[0]); // prépare l'insertion dans la BD
           $em->flush();
           $this->init();            
           $commentaire=$this->repositoryCom->findAll();
           $utilisateurs =$this->repositoryUtil->findAll();
           $Pub=$this->repositoryPub->findBy(array('type'=>$type,'user'=>$user),array('date' => 'DESC'));
           return $this->render('publications.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire));


         }
         $commentaire=$this->repositoryCom->findAll();
         return $this->render('publications.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire));

       }else{
        return $this->render('index.html.twig');
      }
    }


/**
     * @Route("/message", name="message")
     */
public function messageAction (Request $request){

 $this->session = $request->getSession();
 if ( $request->getMethod() == 'POST') {
  if ($this->session->has('id')){
   $this->init(); 
   $now=date("Y-m-d H:i:s"); 
   $date =  new \DateTime($now);
   $message= new Messages();
   $message->setMessage(trim($_POST['message']));
   $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
   $message->setEmetteur($user);
   $message->setDate($date);
   $em = $this->getDoctrine()->getManager();
   $em->persist($message); 
   $em->flush(); 


   $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
   $commentaire=$this->repositoryCom->findAll();
   $messages =$this->repositoryMes->findAll();


   return $this->render('accueil.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));

 } return $this->render('index.html.twig'); 
}else{
  $this->init();
  $Pub=$this->repositoryPub->findBy(array('type'=> array('photo','video','document','article')),array('date' => 'DESC'));
  $commentaire=$this->repositoryCom->findAll();
  $messages =$this->repositoryMes->findAll();
  return $this->render('accueil.html.twig',array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user'),'publication'=>$Pub,'commentaire'=>$commentaire,'messages'=>$messages));
}

}

/**
* @Route("/profile", name="profile")
*
*/
public function profileAction (Request $request){
  $this->session=$request->getSession();
  if ( $this->session->has('id')) {
    $this->init();
    $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));

    if ($request->getMethod() == 'POST') {

      if ($_FILES['file']['size']>0) {
        $file =$_FILES['file']['tmp_name'];
        $nomdufichier=$_FILES['file']['name'];
        $extention=explode(".",$nomdufichier);

        if(($extention[count($extention)-1]=="jpg")||
         ($extention[count($extention)-1]=="JPG")||
         ($extention[count($extention)-1]=="jpeg")||
         ($extention[count($extention)-1]=="JPEG")||
         ($extention[count($extention)-1]=="PNG")||
         ($extention[count($extention)-1]=="png")){
          //unlink($user->getChemin());
        $destination='Upload/Images/'.$nomdufichier;
        $user->setImage($destination);
        move_uploaded_file($file,$destination);
        $em = $this->getDoctrine()->getManager();
   $em->persist($user); 
   $em->flush();

      }else{
        return $this->render('profile.html.twig', array('ErreurPub'=>$this->ErreurPub,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user')));
      }
    }
    if ($_POST['username']) {

       $listUtil = $this->repositoryUtil->findBy( array('nom' => trim($_POST['username'])));

  if (count($listUtil)>=1) {
    return $this->render('profile.html.twig', array('ErreurInscription'=>$this->ErreurInscription,'profile'=>$this->session->get('profile'),'user'=>$this->session->get('user')));

  }else{
    $user->setNom(trim($_POST['username']));
     $em = $this->getDoctrine()->getManager();
   $em->persist($user); 
   $em->flush(); 
  
  }

    }
    if ($_POST['password']) {

    $user->setPassword(md5($_POST['password']));
     $em = $this->getDoctrine()->getManager();
   $em->persist($user); 
   $em->flush(); 
  }
    $user=$this->repositoryUtil->findOneBy(array('id' =>$this->session->get('id')));
         $this->session->set('id',$user->getId()); 
         $this->session->set('user',$user->getNom()); 
         $this->session->set('profile',$user->getImage()); 

  return $this->render('profile.html.twig', array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user')));


}
return $this->render('profile.html.twig', array('profile'=>$this->session->get('profile'),'user'=>$this->session->get('user')));


}
return $this->render('about.html.twig');

}




  
}

