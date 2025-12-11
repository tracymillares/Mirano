<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ----------------------------
        // 1. SUPER ADMIN
        // ----------------------------
        $admin = new User();
        $admin->setUsername('admin01');
        $admin->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        // ----------------------------
        // 2. SAMPLE STAFF
        // ----------------------------
        $staff = new User();
        $staff->setUsername('staff01');
        $staff->setRoles(['ROLE_STAFF']);

        $hashedPassword = $this->passwordHasher->hashPassword($staff, 'staff123');
        $staff->setPassword($hashedPassword);

        $manager->persist($staff);

        // SAVE
        $manager->flush();
    }
}
