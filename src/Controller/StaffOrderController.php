<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Menu;
use App\Form\AdminStaffOrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaffOrderController extends AbstractController
{
    #[Route('/staff/order/new/{id}', name: 'staff_new_order')]
    public function new(
        Menu $menu,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $order = new Order();
        $order->setMenu($menu);

        $form = $this->createForm(AdminStaffOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ TOTAL PRICE IS ALWAYS CALCULATED HERE
            $quantity = $order->getQuantity();
            $price = $menu->getPrice();
            $total = $price * $quantity;

            $order->setTotalPrice(number_format($total, 2, '.', ''));
            $order->setCreatedBy($this->getUser());

            $em->persist($order);
            $em->flush();

            $this->addFlash('success', 'Order successfully created.');

            return $this->redirectToRoute('staff_records_index');
        }

        return $this->render('staff_records/new_order.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu, // 🔥 REQUIRED
        ]);
    }
}
