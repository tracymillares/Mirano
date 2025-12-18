<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Reservation;
use App\Form\StaffOrderType;
use App\Form\ReservationType;
use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
use App\Service\ActivityLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/staff/records')]
class StaffRecordsController extends AbstractController
{
    // 📌 List own records
    #[Route('/', name: 'staff_records_index')]
    public function index(
        OrderRepository $orderRepo,
        ReservationRepository $reservationRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $user = $this->getUser();

        return $this->render('staff_records/index.html.twig', [
            'orders' => $orderRepo->findBy(['createdBy' => $user]),
            'reservations' => $reservationRepo->findBy(['createdBy' => $user]),
        ]);
    }

    // 📌 Create Order
    #[Route('/order/new', name: 'staff_order_new')]
    public function newOrder(
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $order = new Order();
        $order->setCreatedBy($this->getUser());

        $form = $this->createForm(StaffOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($order);
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_CREATE_ORDER',
                'Order ID: ' . $order->getId()
            );

            $this->addFlash('success', 'Order created.');
            return $this->redirectToRoute('staff_records_index');
        }

        return $this->render('staff_records/new_order.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 📌 Create Reservation
    #[Route('/reservation/new', name: 'staff_reservation_new')]
    public function newReservation(
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $reservation = new Reservation();
        $reservation->setCreatedBy($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_CREATE_RESERVATION',
                'Reservation ID: ' . $reservation->getId()
            );

            $this->addFlash('success', 'Reservation created.');
            return $this->redirectToRoute('staff_records_index');
        }

        return $this->render('staff_records/new_reservation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 📌 Edit Order
    #[Route('/order/{id}/edit', name: 'staff_order_edit')]
    public function editOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        if ($order->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(StaffOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_EDIT_ORDER',
                'Order ID: ' . $order->getId()
            );

            $this->addFlash('success', 'Order updated.');
            return $this->redirectToRoute('staff_records_index');
        }

        return $this->render('staff_records/edit_order.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    // 📌 Edit Reservation
    #[Route('/reservation/{id}/edit', name: 'staff_reservation_edit')]
    public function editReservation(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        if ($reservation->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_EDIT_RESERVATION',
                'Reservation ID: ' . $reservation->getId()
            );

            $this->addFlash('success', 'Reservation updated.');
            return $this->redirectToRoute('staff_records_index');
        }

        return $this->render('staff_records/edit_reservation.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }

    // 📌 Delete Order
    #[Route('/order/{id}/delete', name: 'staff_order_delete', methods: ['POST'])]
    public function deleteOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        if ($order->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete_order'.$order->getId(), $request->request->get('_token'))) {
            $em->remove($order);
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_DELETE_ORDER',
                'Order ID: ' . $order->getId()
            );

            $this->addFlash('success', 'Order deleted.');
        }

        return $this->redirectToRoute('staff_records_index');
    }

    // 📌 Delete Reservation
    #[Route('/reservation/{id}/delete', name: 'staff_reservation_delete', methods: ['POST'])]
    public function deleteReservation(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $em,
        ActivityLogger $activityLogger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        if ($reservation->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete_reservation'.$reservation->getId(), $request->request->get('_token'))) {
            $em->remove($reservation);
            $em->flush();

            // ✅ LOG
            $activityLogger->log(
                'STAFF_DELETE_RESERVATION',
                'Reservation ID: ' . $reservation->getId()
            );

            $this->addFlash('success', 'Reservation deleted.');
        }

        return $this->redirectToRoute('staff_records_index');
    }
}
