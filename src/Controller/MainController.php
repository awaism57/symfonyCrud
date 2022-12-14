<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Entity\Logs;
use App\Entity\Student;
use App\Form\CrudType;
use App\Services\UserService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="main")
     */
    public function index(UserService $user): Response
    {
        $logs =$user->getUsers();  

        $data = $this->getDoctrine()->getRepository(Crud::class)->findAll();
        return $this->render('main/index.html.twig', [
            'result' => $data,
            'Logs' => $logs
        ]);
    }


     /**
     * @Route("/logs", name="logs")
     */
    public function logs(UserService $user): Response
    {
        $logs =$user->getUsers();  

        return $this->render('main/logs.html.twig', [
            'Logs' => $logs
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request){
        $crud = new Crud();
        $log = new Logs();
        $date = date("Y-m-d H:i:s");
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($crud);
            $em->flush();

            $log->setaction('Record Created Succefully'.'=> ID : '.$crud->getId());
            $log->setCreatAt($date);
            $em->persist($log);
            
            $em->flush();

            $this->addFlash('notice','Submitted Succefully!');

            return $this->redirectToRoute('main');
        }

        return $this->render('main/create.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id)
    {
        $data = $this->getDoctrine()->getRepository(Crud::class)->find($id);
        $log = new Logs();
        $date = date("Y-m-d H:i:s");
        
        if (!$data) {
            throw $this->createNotFoundException('No Data found for id '.$id);
        }

        $em = $this->getDoctrine()->getManager();
        $log->setaction('Record Deleted Succefully '.'=> ID : '.$id);
        $log->setCreatAt($date);
        $em->persist($log);
        $em->remove($data);
        $em->flush();

        $this->addFlash('notice','Deleted Succefully!');

        return $this->redirectToRoute('main');
    }


    /**
     * @Route("/update/{id}", name="update")
     */

    public function update(Request $request,$id)
    {
        $crud = $this->getDoctrine()->getRepository(Crud::class)->find($id);
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);
        $log = new Logs();
        $date = date("Y-m-d H:i:s");
    

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $log->setaction('Record Updated Succefully '.'=> ID : '.$id);
            $log->setCreatAt($date);
            $em->persist($log);
            $em->persist($crud);
            $em->flush();

            $this->addFlash('notice','Updated Succefully!');

            return $this->redirectToRoute('main');
        }

        return $this->render('main/update.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/one-to-one-self-joining")
     * @return Response
     */

    public function oneToOneSelfJoining(){

        $mentor = new Student();
        $this->entityManager->persist($mentor);

        $newStudent = new Student();
        $newStudent->setMentor($mentor);
        $this->entityManager->persist($newStudent);

        $this->entityManager->flush();

        return new Response(sprintf('Mentor record created with id %d and new Student record created with id %d',
        $mentor->getId(), $newStudent->getId()));


    }


}
