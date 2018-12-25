<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\CompanyCodeHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByOrderNoBeginnings($orderNo)
    {
        $firstThreeChar = substr($orderNo, 0, 3);
        $company = $this->findOneBy(array('code' => $firstThreeChar));
        if(!$company)
            return 'Ecommerce company not found for given code';
        else
            return $company;
    }

    public function findByShippingCodeBeginnings($shippingCode)
    {
        $firstChar = substr($shippingCode, 0, 1);
        $cargoCompany = $this->findOneBy(array('code' => $firstChar));
        if(!$cargoCompany)
            return 'Ecommerce company not found for given code';
        else
            return $cargoCompany;

    }

}
