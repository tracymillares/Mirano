<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Form\AdminStaffOrderType;
use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ActivityLogger;

#[Route('/admin/records')]
class AdminRecordsController extends AbstractController
{
    #[Route('/', name: 'admin_records_index')]
    public function index(
        MenuRepository $menuRepo,
        OrderRepository $orderRepo,
        ReservationRepository $reservationRepo
    ): Response
    {
        $menus = $menuRepo->findAll();
        $orders = $orderRepo->findAll();
        $reservations = $reservationRepo->findAll();

        return $this->render('admin_records/index.html.twig', [
            'menus' => $menus,
            'orders' => $orders,
            'reservations' => $reservations
        ]);
    }

    #[Route('/menu/{id}/delete', name: 'admin_menu_delete', methods:['POST'])]
    public function deleteMenu(
    Menu $menu,
    EntityManagerInterface $em,
    Request $request,
    ActivityLogger $activityLogger
): Response {
    if ($this->isCsrfTokenValid('delete_menu'.$menu->getId(), $request->request->get('_token'))) {

        $menuName = $menu->getName(); // capture BEFORE delete

        $em->remove($menu);
        $em->flush();

        $activityLogger->log(
            'DELETE',
            'Menu: '.$menuName
        );

        $this->addFlash('success', 'Menu item deleted.');
    }

    return $this->redirectToRoute('admin_records_index');
}

    #[Route('/order/{id}/delete', name: 'admin_order_delete', methods:['POST'])]
   public function deleteOrder(
    Order $order,
    EntityManagerInterface $em,
    Request $request,
    ActivityLogger $activityLogger
): Response {
    if ($this->isCsrfTokenValid('delete_order'.$order->getId(), $request->request->get('_token'))) {

        $orderId = $order->getId();

        $em->remove($order);
        $em->flush();

        $activityLogger->log(
            'DELETE',
            'Order ID: '.$orderId
        );

        $this->addFlash('success', 'Order deleted.');
    }

    return $this->redirectToRoute('admin_records_index');
}


    #[Route('/reservation/{id}/delete', name: 'admin_reservation_delete', methods:['POST'])]
    public function deleteReservation(
    Reservation $reservation,
    EntityManagerInterface $em,
    Request $request,
    ActivityLogger $activityLogger
): Response {
    if ($this->isCsrfTokenValid('delete_reservation'.$reservation->getId(), $request->request->get('_token'))) {

        $reservationId = $reservation->getId();

        $em->remove($reservation);
        $em->flush();

        $activityLogger->log(
            'DELETE',
            'Reservation ID: '.$reservationId
        );

        $this->addFlash('success', 'Reservation deleted.');
    }

    return $this->redirectToRoute('admin_records_index');
}



    // Orders
#[Route('/order/{id}/status', name: 'admin_order_update_status', methods:['POST'])]
public function updateOrderStatus(
    Order $order,
    Request $request,
    EntityManagerInterface $em,
    ActivityLogger $activityLogger
): Response {
    $status = $request->request->get('status');

    if ($status) {
        $oldStatus = $order->getStatus();
        $order->setStatus($status);
        $em->flush();

        $activityLogger->log(
            'UPDATE',
            sprintf(
                'Order ID: %d | Status: %s → %s',
                $order->getId(),
                $oldStatus,
                $status
            )
        );

        $this->addFlash('success', 'Order status updated.');
    }

    return $this->redirectToRoute('admin_records_index');
}


// Reservations
public function updateReservationStatus(
    Reservation $reservation,
    Request $request,
    EntityManagerInterface $em,
    ActivityLogger $activityLogger
): Response {
    $status = $request->request->get('status');

    if ($status) {
        $oldStatus = $reservation->getStatus();
        $reservation->setStatus($status);
        $em->flush();

        $activityLogger->log(
            'UPDATE',
            sprintf(
                'Reservation ID: %d | Status: %s → %s',
                $reservation->getId(),
                $oldStatus,
                $status
            )
        );

        $this->addFlash('success', 'Reservation status updated.');
    }

    return $this->redirectToRoute('admin_records_index');
}

#[Route('/reservation/{id}/edit', name: 'admin_reservation_edit', methods: ['GET','POST'])]
public function editReservation(Request $request, Reservation $reservation, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
{
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em->flush();

    $activityLogger->log(
        'UPDATE',
        'Reservation ID: '.$reservation->getId()
    );

    $this->addFlash('success', 'Reservation updated.');
    return $this->redirectToRoute('admin_records_index');
}


    return $this->render('admin_records/edit_reservation.html.twig', [
        'form' => $form,
        'reservation' => $reservation,
    ]);
}

#[Route('/admin/records/{id}/edit', name: 'admin_order_edit')]
public function edit(Request $request, Order $order, EntityManagerInterface $em, ActivityLogger $activityLogger
): Response {
    $form = $this->createForm(AdminStaffOrderType::class, $order);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {

    $order->calculateTotal();
    $em->flush();

    $activityLogger->log(
        'UPDATE',
        'Order ID: '.$order->getId()
    );

    $this->addFlash('success', 'Order updated successfully.');
    return $this->redirectToRoute('admin_records_index');
}


    return $this->render('admin_records/edit_order.html.twig', [
        'form' => $form,
        'order' => $order,
    ]);
}

}
