<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Menu;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/{id}', name: 'app_order')]
    public function order(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        $order = new Order();
        $order->setMenu($menu); // connect menu item

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalPrice = $menu->getPrice() * $order->getQuantity();
            $order->setTotalPrice($totalPrice);
            $order->setOrderDate(new \DateTime());
            $order->setStatus('Pending');

            $em->persist($order);
            $em->flush();

            $this->addFlash('success', 'Your order has been placed successfully!');

            return $this->redirectToRoute('app_order_success', ['id' => $order->getId()]);
        }

        return $this->render('order/order_page.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/success/{id}', name: 'app_order_success')]
    public function success(Order $order): Response
    {
        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }
}
