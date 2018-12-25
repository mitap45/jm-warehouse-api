<?php
/**
 * Created by PhpStorm.
 * User: mitap
 * Date: 25.12.2018
 * Time: 12:19
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CompanyCodeHelper
{
        //Ecommerce company orderNo beginnings
        const ECOMMERCE_COMPANY1_BEGINNING = 'XTR';
        const ECOMMERCE_COMPANY2_BEGINNING = 'MKT';
        const ECOMMERCE_COMPANY3_BEGINNING = 'CPU';
        const ECOMMERCE_COMPANY_CODE_BEGINNINGS = ['XTR', 'MKT', 'CPU'];

        //Cargo company orderNo beginnings
        const CARGO_COMPANYA_BEGINNING = 'A';
        const CARGO_COMPANYB_BEGINNING = 'B';
        const CARGO_COMPANYC_BEGINNING = 'C';
        const CARGO_COMPANY_CODE_BEGINNINGS = ['A', 'B', 'C'];

        const ORDER_NO_LIMIT = 12;

        private $em;
        public function __construct(EntityManagerInterface $entityManagerInterface)
        {
            $this->em = $entityManagerInterface;
        }


        /**
         * Checking If the orderNo is valid or not by given Constants
         * @param string $orderNo
         * @return bool
         */
        public static function validateOrderNo(string $orderNo) : bool
        {
            $firstThreeChar = substr($orderNo, 0, 3);
            $codeBeginningIsValid = in_array($firstThreeChar, self::ECOMMERCE_COMPANY_CODE_BEGINNINGS, true);
            if(!$codeBeginningIsValid || strlen($orderNo) !== self::ORDER_NO_LIMIT)
                return false;

            return true;

        }

        /**
         * Checking if the given orderNo is unique or not
         * @param string $orderNo
         * @return bool
         */
        public function isOrderNoUnique(string $orderNo) : bool
        {
            $qb = $this->em->getRepository('App\Entity\Order')->createQueryBuilder('o');
            $qb
                ->select('o.orderNo')
            ;
            $results = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            foreach ($results as $result)
            {
                if($result['orderNo'] === $orderNo)
                    return false;
            }

            return true;
        }


}