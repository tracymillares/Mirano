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
use App\Service\ActivityLogger;

class AdminOrderController extends AbstractController
{
    #[Route('/admin/order/new/{id}', name: 'admin_new_order')]
    public function new(
        Menu $menu,
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger // ✅ INJECT HERE
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $order = new Order();
        $order->setMenu($menu);

        $form = $this->createForm(AdminStaffOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Calculate total
            $quantity = $order->getQuantity();
            $price = $menu->getPrice();
            $total = $price * $quantity;

            $order->setTotalPrice($total);
            $order->setCreatedBy($this->getUser());

            $em->persist($order);
            $em->flush();

            // ✅ LOG ACTIVITY (AFTER FLUSH — ID EXISTS)
            $activityLogger->log(
                'CREATE',
                sprintf(
                    'Order ID: %d | Menu: %s | Qty: %d',
                    $order->getId(),
                    $menu->getName(),
                    $quantity
                )
            );

            $this->addFlash('success', 'Order successfully created.');

            return $this->redirectToRoute('admin_records_index');
        }

        return $this->render('admin_records/new_order.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }
}
