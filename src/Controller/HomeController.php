<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function index(DishRepository $dishRepository): Response {

        $dishList = $dishRepository->findAll();

        $random = array_rand($dishList, 2);

        return $this->render('home/index.html.twig', [
                    'dish1' => $dishList[$random[0]],
                    'dish2' => $dishList[$random[1]]
        ]);
    }

}
