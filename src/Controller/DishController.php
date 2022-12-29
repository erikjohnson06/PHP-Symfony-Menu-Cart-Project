<?php

namespace App\Controller;

use App\Form\DishType;
use App\Entity\Dish;
use App\Repository\DishRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dish", name="dish.")
 */
class DishController extends AbstractController {

    /**
     * @Route("/", name="edit")
     */
    public function index(DishRepository $dishRepository): Response {

        //Restrict to authenticated users only
        if (!$this->getUser()) {
            $this->addFlash('error', "Employee login required");
            return $this->redirect($this->generateUrl("app_login"));
        }

        $dishList = $dishRepository->findAll();

        return $this->render('dish/index.html.twig', [
                    'dishList' => $dishList
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, ManagerRegistry $doctrine): Response {

        $dish = new Dish();

        //Form
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $image = $request->files->get('dish')['attachment'];
            //$image = $form->get("image")->getData();

            if ($image) {
                $filename = md5(uniqid()) . "." . $image->guessClientExtension();

                $image->move(
                        $this->getParameter("images_folder"),
                        $filename
                );

                $dish->setImage($filename);
            }

            //Entity Manager
            $entityMgr = $doctrine->getManager();
            $entityMgr->persist($dish);
            $entityMgr->flush();

            return $this->redirect($this->generateUrl("dish.edit"));
        }

        return $this->render('dish/create.html.twig', [
                    'createForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id = 0, DishRepository $dishRepository, ManagerRegistry $doctrine): Response {

        //Entity Manager
        $entityMgr = $doctrine->getManager();
        $dish = $dishRepository->find((int) $id);

        if ($dish) {
            $entityMgr->remove($dish);
            $entityMgr->flush();

            $this->addFlash("success", "Dish was removed successfully.");
        } 
        else {
            $this->addFlash("error", "An error occurred deleting this dish.");
        }

        return $this->redirect($this->generateUrl("dish.edit"));
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Dish $dish): Response {

        return $this->render('dish/show.html.twig', [
                    'dish' => $dish
        ]);
    }

}
