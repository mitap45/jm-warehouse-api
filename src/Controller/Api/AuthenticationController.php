<?php

namespace App\Controller\Api;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserSession;

class AuthenticationController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function login(Request $request)
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

        $username = $data['username'];
        $password = $data['password'];

        // username and password is required!!
        if(!array_key_exists('username', $data) || !array_key_exists('password', $data))
        {
            return new Response(
                'username and password is required',
                400,
                array('content-type' => 'application/json')
            );
        }


        //checking if the user found if not then return 404 response with message
        $user = $this->em->getRepository('App\Entity\User')->findOneBy(array('username' => $username));
        if (!$user)
        {
            return new Response(
                'We could not find user by this username',
                404,
                array('content-type' => 'application/json')
            );
        }

        //Checking password and if password is not true returning 401 with error message
        $isPasswordTrue = password_verify($password, $user->getPassword());
        if(!$isPasswordTrue)
        {
            return new Response(
                'Your password is incorrect',
                401,
                array('content-type' => 'application/json')
            );
        }

        $token = $this->getTokenForUser($data, $user);

        $response = array(
            'message' => 'Login was successful',
            'token' => $token
        );

        return new JsonResponse($response);
        
    }

    private function getTokenForUser(array $requestData, User $user) : string
    {
        //If no token was not sent then we create a new token for this user.
        if(!array_key_exists('token', $requestData))
            $userSession = $this->createNewToken($user);
        else
        {
            $token = $requestData['token'];

            // Getting token for that user.If we cant get it
            // then it means token and user does not match
            // So we need to create new token for this user and delete given token if exist
            $userSession = $this->em->getRepository('App\Entity\Token')->findByTokenAndUser($token, $user->getId());

            // If token and user does not match then we create new token for that user
            // And deleting old token
            if(!$userSession)
            {
                //First we remove old token if exist then creating new token
                $token = $this->em->getRepository('App\Entity\Token')->findOneBy(array('token' => $token));
                if($token)
                    $this->em->remove($token);

                $userSession = $this->createNewToken($user);
            }
        }

        return $userSession->getToken();

    }

    /**
     * Creating new token for given user
     * @param User $user
     * @return Token
     */
    private function createNewToken(User $user) : Token
    {
        //Generating new token based on random numbers, time and user id
        $token = sha1(base64_encode(time() . $user->getId() . rand(0, 1000) . rand(0, 500) . rand(0, 100)));
        $newToken = new Token();
        $newToken->setToken($token);
        $newToken->setUser($user);
        $this->em->persist($newToken);
        $this->em->flush();
        return $newToken;
    }

}
