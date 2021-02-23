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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('listdoctor');
    }

    
    /** 
     * @Route("/admin/ajouter", name="ajouter")
    */
    public function ajouter(Request $request)
    {
        $doctor =new doctor();

        $form = $this->createFormBuilder($doctor)
        ->add('fullname', TextType ::class)
        ->add('categorie', TextType ::class)
        ->add('description', TextareaType ::class)
        ->add('image', TextType ::class)
        ->add('phonenumber', TelType ::class)
        ->add('save', SubmitType ::class)
        ->getForm();
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em=$this->getDoctrine()->getManager();
                $em->persist($doctor);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice','doctor bien enregistre.');
                return $this->redirectToRoute('listdoctor',array('id'=>$doctor->getId()));
            }
        }
        return $this->render('admin/ajouter.html.twig',
                array('form'=> $form->createview())); 
    }

   
    /**
     * @Route("/admin/listdoctor",name="listdoctor")
     * 
     */
    public function doctorList(){
        $repository=$this->getDoctrine()->getManager()->getRepository(Doctor::class);
        $doctors=$repository->findAll(); 
        return $this->render('admin/admin.html.twig',['doctors' => $doctors]);
    }

    /**
     * @Route("/admin/supprimer/{id}", name="supp_doctor")
     */
    public function supprimer($id)
    {
        $em =$this->getDoctrine()->getManager();
        $doctor = $em->getRepository(Doctor::class)->find($id);

        if($doctor){
            $em->remove($doctor);
            $em->flush();
        }
        return $this->redirectToRoute('listdoctor');
    }

     /**
     * @Route("/admin/edit/{id}", name="edit_doctor", methods={"GET","POST"})
     */
    public function edit($id, Request $request )

    {
        $doctor=$this->getDoctrine()
            ->getManager()
            ->getRepository(Doctor::class)
            ->find($id);
        $form=$this->createFormbuilder($doctor)
        ->add('fullname', TextType ::class)
        ->add('categorie', TextType ::class)
        ->add('description', TextareaType ::class)
        ->add('image', TextType ::class)
        ->add('phonenumber', TelType ::class)
        ->add('save', SubmitType ::class)
        ->getForm();
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isvalid()){
                $em=$this->getDoctrine()->getManager();
                $em->persist($doctor);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice','client bien modifié');
                return $this->redirectToRoute('listdoctor');
            }
            return $this->render('admin/edit.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('admin/edit.html.twig',array('form'=>$form->createView()));
    }

    /**
     * @Route("/admin/listappointment",name="listappointment")
     * 
     */
    public function appointment_List(){
        $repository=$this->getDoctrine()->getManager()->getRepository(Appointments::class);
        $appoi=$repository->findAll(); 
        return $this->render('admin/list-appointment.html.twig',['appointments' => $appoi]);
    }
}
