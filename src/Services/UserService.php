<?php
// src/Services/UserService.php
namespace App\Services;

use App\Entity\Logs;
// 1. Import the ORM EntityManager Interface
use Doctrine\ORM\EntityManagerInterface;


class UserService {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsers(){

        $em = $this->entityManager;
        
        // A. Access repositories
        $repo = $em->getRepository(Logs::class);
        $res3 = $repo->findAll();
        return $res3;


    }
}