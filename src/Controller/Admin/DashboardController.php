<?php

namespace App\Controller\Admin;

use App\Entity\Dish;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController {

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response {
        
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(DishCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard {
        return Dashboard::new()
                        ->setTitle('Menu Cart | Admin');
    }

    public function configureMenuItems(): iterable {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section("Menu Cart");
        yield MenuItem::linkToCrud('Dishes', 'fa fa-cube', Dish::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Orders', 'fa fa-list', Order::class);
        yield MenuItem::linkToCrud('Order Statuses', 'fa fa-list', OrderStatus::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
    }

}
