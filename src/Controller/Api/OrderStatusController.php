<?php
/**
 * Created by PhpStorm.
 * User: mitap
 * Date: 25.12.2018
 * Time: 16:57
 */

namespace App\Controller\Api;

use App\Constants\OrderStatusConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class OrderStatusController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function shippingStatus(Request $request)
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

        $orderStatus = $this->em->getRepository('App\Entity\OrderStatus')->getOrderStatusByOrderId($order->getId());
        if ($orderStatus->getStatus() === OrderStatusConstants::ORDER_SHIPPING)
        {
            $response = array(
                'shippingDate' => $orderStatus->getCreateDate(),
                'shippingCode' => $orderStatus->getShippingCode(),
            );
        }
        else
        {
            $response = array(
                'message' => 'Orders current status is '.$orderStatus->getStatus(),
            );
        }

        return new JsonResponse($response);

    }

    public function deliveryStatus(Request $request)
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

        $orderStatus = $this->em->getRepository('App\Entity\OrderStatus')->getOrderStatusByOrderId($order->getId());
        if ($orderStatus->getStatus() === OrderStatusConstants::ORDER_DELIVERED)
        {
            $response = array(
                'deliveryDate' => $orderStatus->getCreateDate(),
                'shippingCode' => $orderStatus->getShippingCode(),
            );
        }
        else
        {
            $response = array(
                'message' => 'Orders current status is '.$orderStatus->getStatus(),
            );
        }

        return new JsonResponse($response);

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