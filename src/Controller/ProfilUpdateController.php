<?php

namespace App\Controller;

use App\Form\ProfilUpdateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilUpdateController extends AbstractController
{
    /**
     * @Route("/profil/update", name="profil_update")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();           // getData() : mèthode qui retourne les données du formulaire

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', "Vos modifications ont été prises en compte ! ");
            return $this->redirectToRoute('home');
        }

        return $this->render('profil_update/index.html.twig', [
            'profilUpdateForm' => $form->createView(),
        ]);
    }
}
