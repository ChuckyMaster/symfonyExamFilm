<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route("/impression", name="app_impression")
     */
    public function index(): Response
    {
        return $this->render('impression/index.html.twig', [
            'controller_name' => 'ImpressionController',
        ]);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Film $film
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("impression/{id}", name="new_impression")
     */
    public function new(Request $request, EntityManagerInterface $manager, Film $film){


        $impression = new Impression();

        $form = $this->createForm(ImpressionType::class, $impression);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $impression->setCreatedAt(new \DateTime());
            $impression->setUser($this->getUser());

            $impression->setFilm($film);


            $manager->persist($impression);
            $manager->flush();

        }

        return $this->redirectToRoute('show_film', ['id'=>$impression->getFilm()->getId()]);

    }


    /**
     * @param Impression $impression
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("impression/delete/{id}", name="delete_impression")
     */
    public function suppr(Impression $impression, EntityManagerInterface $manager){

            if($impression && $impression->getUser() == $this->getUser()) {
                $manager->remove($impression);

                $manager->flush();




            }


        return $this->redirectToRoute("show_film", [ 'id'=>$impression->getFilm()->getId()]);



    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Impression $impression
     * @return Response
     *
     * @Route("impression/edit/{id}", name="edit_impression")
     */
    public function change(Request $request, EntityManagerInterface $manager, Impression $impression){

        $form = $this->createForm(ImpressionType::class, $impression);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($impression);
            $manager->flush();

            return $this->redirectToRoute("show_film", ['id'=>$impression->getFilm()->getId()]);
        }

        return $this->renderForm('impression/edit.html.twig', ['form' => $form]);


    }
}
