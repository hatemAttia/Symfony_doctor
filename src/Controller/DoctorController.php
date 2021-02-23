<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Appointments;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DoctorController extends AbstractController
{

    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('doctor/home.html.twig');
    }

    /** 
     * @Route("/home_app", name="ajouter_appoi")
     */
    public function ajouter(Request $request)
    {
        $appoi = new Appointments();
        $form = $this->createFormBuilder($appoi)
            ->add('name', TextType::class,)
            ->add('email', TextType::class)
            ->add('date', DateType::class)
            ->add('phone', TelType::class)
            ->add('doctor', EntityType::class, [
                'class' => Doctor::class,
                'choice_label' => 'fullname',
            ])
            ->add('save', SubmitType::class)

            ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($appoi);
                $em->flush();
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->addFlash('success', 'Your appointment request has been sent successfully. Thank you!');
                }
                $request->getSession()->getFlashBag()->add('notice', 'doctor bien enregistre.');
                return $this->redirectToRoute('ajouter_appoi');
            }
        }
        //----------doctors----------
        $repository = $this->getDoctrine()->getManager()->getRepository(Doctor::class);
        $doctors = $repository->findAll();

        return $this->render(
            'doctor/home.html.twig',
            [
                'form' => $form->createView(),
                'doctors' => $doctors
            ]);
    }
}
