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

            // Calculate total price
            $quantity = $order->getQuantity() ?? 1;
            $totalPrice = $menu->getPrice() * $quantity;
            $order->setTotalPrice($totalPrice);

            // Set order date and default status
            $order->setOrderDate(new \DateTime());
            $order->setStatus('Pending');

            // Set the user who created the order (if logged in)
            if ($this->getUser()) {
                $order->setCreatedBy($this->getUser());
            }

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
