<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use AppBundle\Entity\User;
use AppBundle\Entity\PhoneNumber;

/**
 * @Route("/api/v1.0")
 */
class ApiController extends Controller
{
    /**
     * Returns all users
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Returns all users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     *
     * @Route("/users", name="api_users", methods={"GET"})
     */
    public function usersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        $data = array();
        foreach ($users as $user) {
            $data[] = array(
                "id" => $user->getId(),
                "firstName" => $user->getFirstName(),
                "lastName" => $user->getLastName(),
                "createdAt" => $user->getCreatedAt()->format("Y-m-d H:i:s"),
                "updatedAt" => $user->getUpdatedAt()->format("Y-m-d H:i:s"),
            );
        }

        return new JsonResponse(array(
            $data
        ));
    }

    /**
     * Returns user's phone numbers
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Returns user's phone numbers",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PhoneNumber::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Phone numbers")
     *
     * @Route("/user/{userId}/phone-numbers", name="api_phone_numbers", methods={"GET"})
     */
    public function phoneNumbersAction($userId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->find($userId);

        if (!$user) {
            return new JsonResponse(array(
                "error" => "User not found"
            ));
        }

        $phoneNumbers = $em->getRepository('AppBundle:PhoneNumber')->findByUser($user);

        $data = array();
        foreach ($phoneNumbers as $phoneNumber) {
            $data[] = array(
                "id" => $phoneNumber->getId(),
                "number" => $phoneNumber->getNumber(),
                "createdAt" => $phoneNumber->getCreatedAt()->format("Y-m-d H:i:s"),
                "updatedAt" => $phoneNumber->getUpdatedAt()->format("Y-m-d H:i:s"),
                "user" => array(
                    "id" => $phoneNumber->getUser()->getId(),
                    "firstName" => $phoneNumber->getUser()->getFirstName(),
                    "lastName" => $phoneNumber->getUser()->getLastName(),
                    "createdAt" => $phoneNumber->getUser()->getCreatedAt()->format("Y-m-d H:i:s"),
                    "updatedAt" => $phoneNumber->getUser()->getUpdatedAt()->format("Y-m-d H:i:s"),
                ),
                "name" => $phoneNumber->getName(),
            );
        }

        return new JsonResponse(array(
            $data
        ));
    }
}
