<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\Panier1Type;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier_index", methods={"GET"})
     */
    public function index(PanierRepository $panierRepository,SessionInterface $session,ProduitRepository $produitRepository)
    {
        $panier=$session->get('panier',[]);
        $panierWithData=[];
        foreach ($panier as $id=>$quantity){
            $panierWithData[]=[
                'produit'=>$produitRepository->find($id),
                'quantity'=>$quantity
            ];
        }

        $total=0;
        foreach ($panierWithData as $item){
            $totalItem=$item['produit']->getPrix() * $item['quantity'];
            $total+=$totalItem;
        }

        return $this->render('panier/index.html.twig', [
            'items'=> $panierWithData,
            'total'=>$total
        ]);
    }
    /**
     * @Route("/add{id}", name="panier_add")
     */
    public function add($id, SessionInterface $session){

        $panier= $session->get('panier',[]);
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else {
            $panier[$id]=1;
        }

        $session->set('panier',$panier);
return $this->redirectToRoute('panier_index');
    }
    /**
     * @Route("/remove{id}", name="panier_remove")
     */
    public function remove($id,SessionInterface $session){
 $panier=$session->get('panier',[]);
if(!empty($panier[$id])){
    unset($panier[$id]);

}
$session->set('panier',$panier);
return $this->redirectToRoute("panier_index");
    }

    /**
     * @Route("/new", name="panier_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = new Panier();
        $form = $this->createForm(Panier1Type::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="panier_show", methods={"GET"})
     */
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="panier_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Panier1Type::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="panier_delete", methods={"POST"})
     */
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }
}
