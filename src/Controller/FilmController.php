<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 *
 *
 *
 */
class FilmController extends AbstractController
{
    /**
     * @Route("/films", name="app_films")
     */
    public function index(FilmRepository $filmRepository): Response
    {

        $films = $filmRepository->findAll();


        return $this->render('film/index.html.twig', [
            'films' => $films,
        ]);
    }


    /**
     * @param Film $film
     * @return Response
     *
     * @Route("/film/{id}", name="show_film")
     */
    public function show(Film $film):Response{

        $impression = new Impression();

        $form = $this->createForm(ImpressionType::class, $impression);


        return $this->renderForm('film/show.html.twig', [
            'film' => $film,
            'form' => $form
            ]);

    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/film/new", name="new_film", priority="2")
     */
    public function new(Request $request, EntityManagerInterface $manager){

        $film = new Film();

        $form = $this->createForm(FilmType::class, $film);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $film->setUser($this->getUser());

            $manager->persist($film);
            $manager->flush();


            return $this->redirectToRoute('app_films');

        }
        return $this->renderForm('film/new.html.twig', [
            'form' => $form

        ]);


    }

    /**
     * @param Film $film
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/delete/film{id}", name="delete_film")
     */
    public function suppr(Film $film, EntityManagerInterface $manager){

        if($film && $film->getUser() == $this->getUser()){
            $manager->remove($film);

            $manager->flush();

        }


        return $this->redirectToRoute("app_films");

    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Film $film
     * @return Response
     *
     * @Route("film/edit/{id}", name="edit_film", priority="3")
     *
     */
    public function change(Request $request, EntityManagerInterface $manager, Film $film){

        $form = $this->createForm(FilmType::class, $film);

        $form->handleRequest($request);

        if($film && $film->getUser() == $this->getUser()) {

            if($form->isSubmitted() && $form->isValid()){

                $manager->persist($film);
                $manager->flush();

                return $this->redirectToRoute("app_films");

            }
        }



        return $this->renderForm('film/edit.html.twig', [
            'form' => $form,
            'film' => $film
        ]);

    }



}
