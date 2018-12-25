<?php
/**
 * Created by PhpStorm.
 * User: mitap
 * Date: 25.12.2018
 * Time: 01:14
 */

namespace App\Controller\Api;


use App\Constants\OrderStatusConstants;
use App\Entity\OrderStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


class OrderCancellationController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function cancel(Request $request)
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


        $order = $this->em->getRepository('App\Entity\Order')->findOneBy(array('orderNo' => $orderNo));
        if(!$order)
        {
            return new Response(
                'Order could not be found for given code',
                404,
                array('content-type' => 'application/json')
            );
        }


        $cancelDate = $data['cancelDate'];
        $cancelDateObject = new \DateTime($cancelDate["date"]);
        $cancelDateObject->setTimezone(new \DateTimeZone($cancelDate["timezone"]));

        $orderStatus = $this->em->getRepository('App\Entity\OrderStatus')->getOrderStatusByOrderId($order->getId());
        if ($orderStatus->getStatus() === OrderStatusConstants::ORDER_TAKEN)
        {
            $this->directCancellation($orderStatus, $cancelDateObject);
        }
        elseif ($orderStatus->getStatus() === OrderStatusConstants::ORDER_SHIPPING)
        {
            $this->shippingCancellation($orderStatus, $cancelDateObject);
        }
        elseif ($orderStatus->getStatus() === OrderStatusConstants::ORDER_DELIVERED)
        {
            $this->deliveryCancellation($orderStatus, $cancelDateObject);
        }
        else
        {
            return new Response(
                'Order has no status or CANCELED status so we can not cancel this order',
                400,
                array('content-type' => 'application/json')
            );
        }

        $this->em->flush();

        $response = array(
            'status' => 'OK',
            'responseCode' => 200
        );

        return new JsonResponse($response);
    }

    //Creating new orderstatus for that order with 'CANCELED' status
    private function directCancellation(OrderStatus $oldStatus, \DateTime $cancelDateObject)
    {
        $orderStatus = new OrderStatus();
        $orderStatus->setStatus(OrderStatusConstants::ORDER_CANCELED);
        $orderStatus->setCreateDate($cancelDateObject);
        $orderStatus->setOrder($oldStatus->getOrder());
        $this->em->persist($orderStatus);

    }

    //Creating new orderstatus for that order with 'CANCELED' status and old shippingCode
    private function shippingCancellation(OrderStatus $oldStatus, \DateTime $cancelDateObject)
    {
        $orderStatus = new OrderStatus();
        $orderStatus->setStatus(OrderStatusConstants::ORDER_CANCELED);
        $orderStatus->setCreateDate($cancelDateObject);
        $orderStatus->setOrder($oldStatus->getOrder());

        //Setting old shipping code to this one so that we know that
        //This order cancelled on shipping process
        $orderStatus->setShippingCode($oldStatus->getShippingCode());
        $this->em->persist($orderStatus);

    }

    //Creating new orderstatus for that order with 'CANCELED' status, old shippingCode, deliveryDate
    private function deliveryCancellation(OrderStatus $oldStatus, \DateTime $cancelDateObject)
    {
        $orderStatus = new OrderStatus();
        $orderStatus->setStatus(OrderStatusConstants::ORDER_CANCELED);
        $orderStatus->setCreateDate($cancelDateObject);
        $orderStatus->setOrder($oldStatus->getOrder());

        //Setting old shipping code and deliveryDate to this one so that we know that
        //This order cancelled after delivery process
        $orderStatus->setShippingCode($oldStatus->getShippingCode());
        $orderStatus->setDeliveryDate($oldStatus->getDeliveryDate());
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


}