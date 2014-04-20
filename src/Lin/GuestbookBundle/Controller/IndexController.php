<?php

namespace Lin\GuestbookBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Lin\GuestbookBundle\Exception\InvalidFormException;
use Lin\GuestbookBundle\Form\GuestType;
use Lin\GuestbookBundle\Model\GuestInterface;


class IndexController extends FOSRestController
{
    /**
     * List all guests.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing guests.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many guests to return.")
     *
     * @Annotations\View(
     *  templateVar="guests"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getGuestsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('lin_guestbook.guest.handler')->all($limit, $offset);
    }

    /**
     * Get single Guest.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a guest for a given id",
     *   output = "Lin\GuestbookBundle\Entity\Guest",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the guest is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="guest")
     *
     * @param int     $id      the guest id
     *
     * @return array
     *
     * @throws NotFoundHttpException when guest not exist
     */
    public function getGuestAction($id)
    {
        $guest = $this->getOr404($id);

        return $guest;
    }

    /**
     * Presents the form to use to create a new guest.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @return FormTypeInterface
     */
    public function newGuestAction()
    {
        return $this->createForm(new GuestType());
    }

    /**
     * Create a Guest from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new guest from the submitted data.",
     *   input = "Lin\GuestbookBundle\Form\GuestType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "LinGuestbookBundle:Index:newGuest.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postGuestAction(Request $request)
    {
        try {
            $newGuest = $this->container->get('lin_guestbook.guest.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newGuest->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_guest', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing guest from the submitted data or create a new guest at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Lin\GuestbookBundle\Form\GuestType",
     *   statusCodes = {
     *     201 = "Returned when the Guest is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "LinGuestbookBundle:Guest:editGuest.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the guest id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when guest not exist
     */
    public function putGuestAction(Request $request, $id)
    {
        try {
            if (!($guest = $this->container->get('lin_guestbook.guest.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $guest = $this->container->get('lin_guestbook.guest.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $guest = $this->container->get('lin_guestbook.guest.handler')->put(
                    $guest,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $guest->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_guest', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing guest from the submitted data or create a new guest at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Lin\GuestbookBundle\Form\GuestType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "LinGuestbookBundle:Guest:editGuest.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the guest id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when guest not exist
     */
    public function patchGuestAction(Request $request, $id)
    {
        try {
            $guest = $this->container->get('lin_guestbook.guest.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $guest->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_guest', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Guest or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return GuestInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($guest = $this->container->get('lin_guestbook.guest.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $guest;
    }
}
