<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivresController extends AbstractController
{
    #[Route('/admin/livres/update/{id}', name: 'app_livres_update')]
    public function update(EntityManagerInterface $em,Livres $livre): Response
    {   $nouveauPrix=$livre->getPrix()*1.1;
        $livre->setPrix($nouveauPrix);
        $em->persist($livre);
        $em->flush();
        dd($livre);

    }
    #[Route('/admin/livres/delete/{id}', name: 'app_livres_delete')]
public function delete(EntityManagerInterface $em,Livres $livre): Response
{
    $em->remove($livre);
    $em->flush();
  dd($livre);

}
    #[Route('/admin/livres', name: 'admin_livres')]
    public function all(LivresRepository $rep,PaginatorInterface $paginator,Request $request): Response
    {
        $query = $rep->findAll();
        $livres = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('livres/all.html.twig', ['livres' => $livres]);
    }
    #[Route('/admin/livres/create', name: 'admin_livres_create')]
    public function create(Request $request,EntityManagerInterface $em): Response
    {  $livre=new Livres();
        //Affichage du formulaire
        $form=$this->createForm(LivresType::class,$livre);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($categorie);
            $em->persist($livre);
            $em->flush();
            $this->addFlash('success','La livre a été ajouté dans la base');

            return $this->redirectToRoute('admin_livres');
        }


        return $this->render
        ('livres/create.html.twig', [
            'f' => $form,
        ]);
    }
//paramconverter
    #[Route('/admin/livres/show/{id}', name: 'app_livres_show')]
    public function show(Livres $livre): Response
    {

     if(!$livre){
         throw $this->createNotFoundException("Livre  n'existe pas");
     }
     return $this->render('livres/show.html.twig', ['livre' => $livre]);
    }

    #[Route('/admin/livres/show2', name: 'app_livres_show2')]
    public function show2(LivresRepository $rep): Response
    {
        $livre = $rep->findOneBy(['titre' => 'titre 1']);
        dd($livre);
    }
    #[Route('/admin/livres/show3', name: 'app_livres_show3')]
    public function show3(LivresRepository $rep): Response
    {
        $livres = $rep->findBy(['titre' => 'titre 1','editeur'=>'Dunod'],['prix'=>'ASC']);
        dd($livres);
    }


}