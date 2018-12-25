<?php

namespace App\Controller\Api;

use App\Constants\OrderStatusConstants;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\OrderStatus;
use App\Entity\User;
use App\Service\CompanyCodeHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


class OrderController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function create(Request $request, CompanyCodeHelper $companyCodeHelper)
    {

        //Checking if the request has any content if not then return bad request
        if(!$request->getContent())
        {
            return new Response(
                'Bad Request',
                400,
                array('content-type' => 'application/json')
            );

        }

        //recieving posted json data that includes username password and token
        $data = json_decode($request->getContent(), true);


        //If there is no token then return 400
        if(!array_key_exists('token', $data))
        {
            return new Response(
                'No token provided',
                400,
                array('content-type' => 'application/json')
            );
        }
        else
            $token = $data['token'];


        //Controlling token
        //If the user and token does not match then we return 401
        $orderNo = $data['orderNo'];
        $tokenControl = $this->tokenControl($token, $orderNo);
        if(!$tokenControl)
        {
            return new Response(
                'Unauthenticated',
                401,
                array('content-type' => 'application/json')
            );

        }

        //Checking if orderNo is valid
        $isOrderNoValid = CompanyCodeHelper::validateOrderNo($orderNo);
        if(!$isOrderNoValid)
        {
            return new Response(
                'OrderNo is not valid',
                400,
                array('content-type' => 'application/json')
            );
        }

        //Checking if orderNo is unique
        $isOrderNoUnique = $companyCodeHelper->isUnique($orderNo);
        if(!$isOrderNoUnique)
        {
            return new Response(
                'Order code is not unique',
                400,
                array('content-type' => 'application/json')
            );
        }


        //Creating new order
        $order = $this->createOrder($data);

        // Creating OrderProducts and checking if we have that products or not
        $items = $data['items'];
        $isThereMissingProduct = $this->addOrderItemsAndCheckMissingItem($order, $items);
        if ($isThereMissingProduct)
        {
            return new Response(
                'One or more product you provided does not exist in warehouse',
                404,
                array('content-type' => 'application/json')
            );

        }

        //Checking Ecommerce company
        $ecommerceCompany = $this->em->getRepository('App\Entity\User')->findByOrderNoBeginnings($orderNo);
        if($ecommerceCompany instanceof User)
        {
            $order->setEcommerceCompany($ecommerceCompany);
        }
        else
        {
            return new Response(
                'order no does not match with our Ecommerce Companies',
                404,
                array('content-type' => 'application/json')
            );
        }

        //Creating order status with ORDER TAKEN statu
        $this->createOrderStatus($order, OrderStatusConstants::ORDER_TAKEN, $data['orderDate']);
        $this->em->persist($order);
        $this->em->flush();

        $response = array(
            'status' => 'OK',
            'responseCode' => 200
        );

        return new JsonResponse($response);
        
    }

    public function update(Request $request, CompanyCodeHelper $companyCodeHelper)
    {

        //Checking if the request has any content if not then return bad request
        if(!$request->getContent())
        {
            return new Response(
                'Bad Request',
                400,
                array('content-type' => 'application/json')
            );

        }

        //recieving posted json data that includes username password and token
        $data = json_decode($request->getContent(), true);

        //If there is no token then return 400
        if(!array_key_exists('token', $data))
        {
            return new Response(
                'No token provided',
                400,
                array('content-type' => 'application/json')
            );
        }
        else
            $token = $data['token'];


        //Controlling token
        //If the user and token does not match then we return 401
        $orderNo = $data['orderNo'];
        $tokenControl = $this->tokenControl($token, $orderNo);
        if(!$tokenControl)
        {
            return new Response(
                'Unauthenticated',
                401,
                array('content-type' => 'application/json')
            );

        }

        //Checking if order exists
        $order = $this->em->getRepository('App\Entity\Order')->findOneBy(array('orderNo' => $orderNo));
        if(!$order)
        {
            return new Response(
                'Order could not be found',
                404,
                array('content-type' => 'application/json')
            );

        }

        // We are checking that if products exist in our db if not then return 404 with message
        $isThereMissingProduct = $this->updateOrderItemsAndCheckMissingItem($order, $data['items']);
        if ($isThereMissingProduct)
        {
            return new Response(
                'One or more product you provided does not exist in warehouse',
                404,
                array('content-type' => 'application/json')
            );

        }


        $orderDate = $data['orderDate'];
        $maxShippingDate = $data['maxShippingDate'];

        $orderDateObject = new \DateTime($orderDate["date"]);
        $orderDateObject->setTimezone(new \DateTimeZone($orderDate["timezone"]));

        $maxShippingDateObject = new \DateTime($maxShippingDate["date"]);
        $maxShippingDateObject->setTimezone(new \DateTimeZone($maxShippingDate["timezone"]));



        //If address has changed and max shipping date is not due yet then we change the address
        //Otherwise we return 400
        if ($this->isShippingAddressChanged($order, $data))
        {
            if(new \DateTime('now') < $order->getMaxShippingDate())
            {
                $order->setPostalCode($data['postalCode']);
                $order->setShippingAddress($data['shippingAddress']);
                $order->setShippingCity($data['shippingCity']);
                $order->setShippingRegion($data['shippingRegion']);
            }
            else
            {
                return new Response(
                    'You can not change the address after max shipping date',
                    400,
                    array('content-type' => 'application/json')
                );

            }

        }

        $order->setCustomerName($data['customerName']);
        $order->setOrderDate($orderDateObject);

        $this->em->flush();

        $response = array(
            'status' => 'OK',
            'responseCode' => 200
        );

        return new JsonResponse($response);

    }



    /**
     * Check itemCodes with our product codes and if one product does not exist then return true
     * If codes are matched then add OrderProducts for given order.
     * Not seperating missing product check as a function because it would increase the number of db query
     * @param Order $order
     * @param array $itemsCodes
     * @return bool
     */
    private function addOrderItemsAndCheckMissingItem(Order $order, array $itemsCodes) : bool
    {
        $isThereMissingProduct = false;
        foreach ($itemsCodes as $itemsCode)
        {
            $product = $this->em->getRepository('App\Entity\Product')->findOneBy(array('code' => $itemsCode));

            //If one product does not exist then canceling the order.
            if (!$product)
                $isThereMissingProduct = true;
            else
            {
                $item = new OrderProduct();
                $item->setProduct($product);
                $item->setAmount(mt_rand(1,100));
                $item->setOrder($order);
                $this->em->persist($item);
            }
        }

        return $isThereMissingProduct;
    }

    /**
     * Check itemCodes to our product codes and if one product does not exist then return true
     * If codes are matched then add OrderProducts for given order.
     * Not seperating missing product check as a function because it would increase the number of db query
     * @param Order $order
     * @param array $itemsCodes
     * @return bool
     */
    private function updateOrderItemsAndCheckMissingItem(Order $order, array $itemsCodes) : bool
    {
        $orderProducts = $order->getItems();
        foreach ($orderProducts as $orderProduct)
        {
            $this->em->remove($orderProduct);
        }

        //Adding order items and checking missing product
        $isThereMissingProduct = $this->addOrderItemsAndCheckMissingItem($order, $itemsCodes);

        return $isThereMissingProduct;
    }

    /**
     * Creating OrderStatus with given status
     * @param Order $order
     * @param string $status
     * @param array $orderDate
     */
    private function createOrderStatus(Order $order, string $status, array $orderDate) : void
    {
        $orderStatus = new OrderStatus();

        $orderDateObject = new \DateTime($orderDate["date"]);
        $orderDateObject->setTimezone(new \DateTimeZone($orderDate["timezone"]));

        $orderStatus->setOrder($order);
        $orderStatus->setStatus($status);
        $orderStatus->setCreateDate($orderDateObject);
        $this->em->persist($orderStatus);
    }

    /**
     * Checking User Session by token
     * @param string $token
     * @param string $orderNo
     * @return bool
     */
    private function tokenControl(string $token, string $orderNo) : bool
    {
        $ecommerceCompany = $this->em->getRepository('App\Entity\User')->findByOrderNoBeginnings($orderNo);
        $userSession = $this->em->getRepository('App\Entity\Token')->findByTokenAndUser($token, $ecommerceCompany->getId());

        if (!$userSession)
            return false;

        return true;
    }

    /**
     * Create order with given data array
     * @param array $requestedData
     * @return Order
     */
    private function createOrder(array $requestedData) : Order
    {
        //Creating new order
        $order = new Order();

        $orderDate = $requestedData['orderDate'];
        $maxShippingDate = $requestedData['maxShippingDate'];

        $orderDateObject = new \DateTime($orderDate["date"]);
        $orderDateObject->setTimezone(new \DateTimeZone($orderDate["timezone"]));

        $maxShippingDateObject = new \DateTime($maxShippingDate["date"]);
        $maxShippingDateObject->setTimezone(new \DateTimeZone($maxShippingDate["timezone"]));



        $order->setOrderNo($requestedData['orderNo']);
        $order->setPostalCode($requestedData['postalCode']);
        $order->setShippingAddress($requestedData['shippingAddress']);
        $order->setShippingCity($requestedData['shippingCity']);
        $order->setShippingRegion($requestedData['shippingRegion']);
        $order->setCustomerName($requestedData['customerName']);
        $order->setMaxShippingDate($maxShippingDateObject);
        $order->setOrderDate($orderDateObject);

        return $order;
    }

    /**
     * Checking if order address changed or not for update operation.
     * @param Order $order
     * @param array $requestedData
     * @return bool
     */
    private function isShippingAddressChanged(Order $order, array $requestedData) : bool
    {
        if(
            $order->getShippingAddress() !== $requestedData['shippingAddress'] ||
            $order->getShippingCity() !== $requestedData['shippingCity'] ||
            $order->getShippingRegion() !== $requestedData['shippingRegion'] ||
            $order->getPostalCode() !== $requestedData['postalCode']
        )
            return true;
        else
            return false;
    }

}
