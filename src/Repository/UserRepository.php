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

        if(CompanyCodeHelper::ECOMMERCE_COMPANY1_BEGINNING === $firstThreeChar)
            return $this->findOneBy(array('username' => 'company1'));
        elseif (CompanyCodeHelper::ECOMMERCE_COMPANY2_BEGINNING === $firstThreeChar)
            return $this->findOneBy(array('username' => 'company2'));
        elseif (CompanyCodeHelper::ECOMMERCE_COMPANY3_BEGINNING === $firstThreeChar)
            return $this->findOneBy(array('username' => 'company3'));
        else
            return 'Ecommerce company not found for given code';

    }

    public function findByShippingCodeBeginnings($shippingCode)
    {
        $firstChar = substr($shippingCode, 0, 1);

        if(CompanyCodeHelper::CARGO_COMPANYA_BEGINNING === $firstChar)
            return $this->findOneBy(array('username' => 'cargoA'));
        elseif (CompanyCodeHelper::CARGO_COMPANYB_BEGINNING === $firstChar)
            return $this->findOneBy(array('username' => 'cargoB'));
        elseif (CompanyCodeHelper::CARGO_COMPANYC_BEGINNING === $firstChar)
            return $this->findOneBy(array('username' => 'cargoC'));
        else
            return 'Ecommerce company not found for given code';

    }

}
