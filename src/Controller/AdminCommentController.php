<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/Commentaire", name="admin_comment_index")
     */
    public function index(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'edition d'un commentaire
     * 
     * @Route("/admin/comments/{id}/edit", name ="admin_comment_edit")
     * 
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, Request $request){
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();
        

        $this->addFlash(
            'success',
            "Le commentaire  n° <strong>{$comment->getId()}</strong> a bien été enregistrée !"
        );
        }

        return $this->render('admin/comment/edit.html.twig',[
        'comment'=>$comment,
        'form' =>$form->createView()
            
        ]);
    }

     /**
     * permet de supprimer un commentaire
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * 
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment){
        
        
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($comment);
            $manager->flush();
       
        $this->addFlash(
            'success',
            "Le commentaire  a bien été supprimée !"
        );
       
        
        return $this->redirectToRoute('admin_comment_index'); 
    }
}

