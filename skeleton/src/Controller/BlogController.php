<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Article;

class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */

    public function home()
    {
        return $this ->render('blog/home.html.twig');
    }



    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo ->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null ,Request $request, ObjectManager $manager)
    {
        if (!$article){

            $article = new Article();
        }


        $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->add('content')
            ->add('image')

            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());

            }



            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' =>$article->getId() !== null
        ]);

    }



    /**
     * @Route("/{id}", name="blog_show")
     */
    public function show($id)
    {
        $repo = $this-> getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        return $this ->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }



}