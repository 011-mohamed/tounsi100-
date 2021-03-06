<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        //$repo = $this->getdoctrine()->getRepository(Ad::class);


        $ads = $repo->findall();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    /**
     * permet de creer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     *  @IsGranted("ROLE_USER")
     * 
     * @return Response 
     */

    public function create(Request $request)
    {
        $ad = new Ad();


        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $manager = $this->getDoctrine()->getManager();
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            //ObjectManager $manager marche pas  !!!
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ad);
            $manager->flush();



            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'edition
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",
     * message="cette annonce ne vous appartient pas , vous ne pouvez pas la modifier")
     * 
     * @return Response 
     */
    public function edit( Ad $ad, Request $request){
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $manager = $this->getDoctrine()->getManager();
                $image->setAd($ad);
                $manager->persist($image);
            }

            //ObjectManager $manager marche pas  !!!
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "les modifications de L'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }





    /**
     * Permet d'afficher une seule annonce 
     * 
     * @Route("/ads/{slug}" , name="ads_show")
     * 
     * @return Response 
     */
    public function show(Ad $ad)
    {

        // paramconverter avec le slug on va recuperer l Ad qui est liee au ce slug 

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    /**
     * permet de supprimer une annonce
     * @Route("/ads/{slug}/delete" , name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",
     * message="vous n'avez pas le droit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function delete(Ad $ad){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
        'success',
        "L annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );
         
        return $this->redirectToRoute('ads_index');
    }
}
