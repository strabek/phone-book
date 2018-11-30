<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Phonenumber controller.
 *
 * @Route("phonenumber")
 */
class PhoneNumberController extends Controller
{
    /**
     * Lists all phoneNumber entities.
     *
     * @Route("/", name="phonenumber_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $phoneNumbers = $em->getRepository('AppBundle:PhoneNumber')->findAll();

        return $this->render('phonenumber/index.html.twig', array(
            'phoneNumbers' => $phoneNumbers,
        ));
    }

    /**
     * Creates a new phoneNumber entity.
     *
     * @Route("/new/{userId}", name="phonenumber_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $userId)
    {
        $phoneNumber = new Phonenumber();
        $form = $this->createForm('AppBundle\Form\PhoneNumberType', $phoneNumber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $user = $em->getRepository('AppBundle:User')->find($userId);
            if ($user) {
                $phoneNumber->setUser($user);
                $em->persist($phoneNumber);
                $em->flush();
            }

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('phonenumber/new.html.twig', array(
            'phoneNumber' => $phoneNumber,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a phoneNumber entity.
     *
     * @Route("/{id}", name="phonenumber_show")
     * @Method("GET")
     */
    public function showAction(PhoneNumber $phoneNumber)
    {
        $deleteForm = $this->createDeleteForm($phoneNumber);

        return $this->render('phonenumber/show.html.twig', array(
            'phoneNumber' => $phoneNumber,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing phoneNumber entity.
     *
     * @Route("/{id}/edit", name="phonenumber_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PhoneNumber $phoneNumber)
    {
        $deleteForm = $this->createDeleteForm($phoneNumber);
        $editForm = $this->createForm('AppBundle\Form\PhoneNumberType', $phoneNumber);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', array('id' => $phoneNumber->getUser()->getId()));
        }

        return $this->render('phonenumber/edit.html.twig', array(
            'phoneNumber' => $phoneNumber,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'userId' => $phoneNumber->getUser()->getId(),
        ));
    }

    /**
     * Deletes a phoneNumber entity.
     *
     * @Route("/{id}", name="phonenumber_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PhoneNumber $phoneNumber)
    {
        $form = $this->createDeleteForm($phoneNumber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($phoneNumber);
            $em->flush();
        }

        return $this->redirectToRoute('phonenumber_index');
    }

    /**
     * Creates a form to delete a phoneNumber entity.
     *
     * @param PhoneNumber $phoneNumber The phoneNumber entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PhoneNumber $phoneNumber)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('phonenumber_delete', array('id' => $phoneNumber->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
