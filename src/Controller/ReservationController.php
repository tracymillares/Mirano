<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Form\ReservationBookType; // <-- Front-end user form
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ActivityLogger;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    // INDEX - List all reservations (Admin)
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    // NEW - Admin form to create a reservation
   #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $reservation = new Reservation();
    $reservation->setCreatedBy($this->getUser()); // ✅ ADMIN OWNER

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $em->persist($reservation);
    $em->flush();

    $activityLogger->log(
        'CREATE',
        'Reservation ID: ' . $reservation->getId()
    );

    return $this->redirectToRoute('app_reservation_index');
}


    return $this->render('reservation/new.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
    ]);
}

    // BOOK - Front-end user reservation form
   #[Route('/book', name: 'app_reservation_book', methods: ['GET', 'POST'])]
public function book(Request $request, EntityManagerInterface $entityManager): Response
{
    $reservation = new Reservation();

    // ✅ OPTIONAL: only set if logged in
    if ($this->getUser()) {
        $reservation->setCreatedBy($this->getUser());
    }

    $form = $this->createForm(ReservationBookType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Your reservation is confirmed!');

        return $this->redirectToRoute('reservation_thank_you');
    }

    return $this->render('reservation/book.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
    ]);
}

    #[Route('/thank-you', name: 'reservation_thank_you', methods: ['GET'])]
public function thankYou(): Response
{
    return $this->render('reservation/thank_you.html.twig');
}


    // SHOW - Admin view single reservation
    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    // EDIT - Admin edit reservation
    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    $em->flush();

    $activityLogger->log(
        'UPDATE',
        'Reservation ID: ' . $reservation->getId()
    );

    return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
}

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    // DELETE - Admin delete reservation
    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
    $em->remove($reservation);
    $em->flush();

    $activityLogger->log(
        'DELETE',
        'Reservation ID: ' . $reservation->getId()
    );
}

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
