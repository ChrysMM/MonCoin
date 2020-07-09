<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Category;
use App\Repository\AnnonceRepository;
use PhpParser\Node\Stmt\Label;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MonCoinController extends AbstractController
{
    /**
     * @Route("/index", name="mon_coin")
     */
    public function index()
    {
        $annonceRepository = $this->getDoctrine()->getRepository(Annonce::class);
        $annonces = $annonceRepository->findAll();
        return $this->render('mon_coin/index.html.twig', [
            'annonces' => $annonces
        ]);
    }
    /**
     * @route("/annonce/{id}", name="mon_coin_annonce")
     **/
     public function annonce($id){
         $annonceRepository = $this->getDoctrine()->getRepository(Annonce::class);
         $annonce = $annonceRepository->find($id);
         return $this->render('mon_coin/annonce.html.twig', [
             'annonce' => $annonce
         ]);
     }
    /**
     * @Route("/add", name="mon_coin_add")
     */
    public function add(Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');//le create
        $user = $this->getUser();
        $annonce = new Annonce();
        
        $annonce->setAuthor($user);
        $form = $this->createFormBuilder($annonce)
        ->add('title', TextType::class)
        ->add('content', TextareaType::class)
        ->add('price', NumberType::class) //on peut utiliser MoneyType mais en centimes
        ->add('city', TextType::class)
        ->add('category', EntityType::class, ['class'=> Category::class])
        ->add('submit', SubmitType::class, ['label'=>'Je vends!'])
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $annonce->setdate(new \DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();
            return $this->redirectToRoute('mon_coin');
        }
        return $this->render('mon_coin/add.html.twig', ['addForm'=> $form->createView()]);
    }
/**
     * @Route("/update/{id}", name="mon_coin_update")
     */
public function update($id, Request $request){
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');//l'update

    $annonce = $this->getDoctrine()->getRepository(Annonce::class)->find($id);
    $user = $this->getUser();
    if ($user != $annonce->getAuthor()){
        return $this->redirectToRoute('mon_coin');
    }

    $form = $this->createFormBuilder($annonce)
        ->add('title', TextType::class)
        ->add('content', TextareaType::class)
        ->add('price', NumberType::class)
        ->add('city', TextType::class)
        ->add('category', EntityType::class, ['class'=> Category::class])
        ->add('submit', SubmitType::class, ['label'=>'Je vends!'])
       
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('mon_coin_annonce', ['id'=> $annonce->getId()]);

        }
        return $this->render('mon_coin/update.html.twig', ['updateForm'=>$form->createView()]);
}
 /**
     * @Route("/delete/{id}", name="mon_coin_delete")
     */
    public function delete($id){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $annonceRepository = $this->getDoctrine()->getRepository(Annonce::class);
        $annonce = $annonceRepository->find($id);
        $user = $this->getUser();
        if ($user != $annonce->getAuthor()){
            return $this->redirectToRoute('mon_coin');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($annonce);
        $entityManager->flush();
        return $this->redirectToRoute('mon_coin');
    }

}