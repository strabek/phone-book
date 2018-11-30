<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $this->container->getParameter('page_size')/*limit per page*/
        );

        return $this->render('user/index.html.twig', array(
            'users' => $pagination,
        ));
    }

    /**
     * @Route("/search", name="search")
     * @Method({"GET"})
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get("query");

        $em = $this->getDoctrine()->getManager();

        $data = 

        $users = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
           ->where('u.firstName LIKE :query')
           ->orWhere('u.lastName LIKE :query')
           ->setParameter('query', '%'.$query.'%')
           ->getQuery()
           ->getResult()
        ;

        $phoneNumbers = $em->getRepository('AppBundle:PhoneNumber')->createQueryBuilder('u')
           ->where('u.name LIKE :query')
           ->orWhere('u.number LIKE :query')
           ->setParameter('query', '%'.$query.'%')
           ->getQuery()
           ->getResult()
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            array_merge($users, $phoneNumbers), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $this->container->getParameter('page_size')/*limit per page*/
        );
// dump($pagination);die;
        return $this->render('search.html.twig', array(
            'items' => $pagination,
        ));
    }
}
