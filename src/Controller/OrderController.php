<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController {

    /**
     * @Route("/orders", name="app_orders")
     */
    public function index(OrderRepository $orderRepository): Response {

        $orders = $orderRepository->findOpenOrders();

        return $this->render('order/index.html.twig', [
                    'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order/{id}", name="app_order")
     */
    public function order(Dish $dish): Response {

        $order = new Order();
        $order->setTableId("table1");
        $order->setName($dish->getName());
        $order->setOrderId($dish->getId());
        $order->setPrice($dish->getPrice());

        $em = $this->getDoctrine()->getManager();

        //Find the "Open" status
        $orderStatus = $em->getRepository(OrderStatus::class)->findBy([
            "name" => "Open"
        ]);

        $order->setStatus($orderStatus[0]);

        $em->persist($order);
        $em->flush();

        $this->addFlash("order", $order->getName() . " was added to the order.");

        return $this->redirect($this->generateUrl("app_menu"));
    }

    /**
     * @Route("/status/{id},{status}", name="app_status")
     */
    public function status($id, $status): Response {

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find((int) $id);

        if (!$order) {
            $this->addFlash("error", "Invalid order ID.");
            return $this->redirect($this->generateUrl("app_orders"));
        }

        $orderStatus = $em->getRepository(OrderStatus::class)->findBy([
            "name" => $status
        ]);

        if (!$orderStatus) {
            $this->addFlash("error", "Invalid order status. Unable to update order.");
            return $this->redirect($this->generateUrl("app_orders"));
        }

        $order->setStatus($orderStatus[0]);

        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl("app_orders"));
    }

    /**
     * @Route("/delete_order/{id}", name="app_delete_order")
     */
    public function delete($id = 0, OrderRepository $orderRepository, ManagerRegistry $doctrine): Response {

        //Entity Manager
        $entityMgr = $doctrine->getManager();
        $order = $orderRepository->find((int) $id);

        if ($order) {
            $entityMgr->remove($order);
            $entityMgr->flush();

            $this->addFlash("success", "Order was removed successfully.");
        }
        else {
            $this->addFlash("error", "An error occurred deleting this order.");
        }

        return $this->redirect($this->generateUrl("app_orders"));
    }

}
