<?php

namespace Lin\GuestbookBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Lin\GuestbookBundle\Entity\Guest;
use Lin\GuestbookBundle\Form\GuestType;
use Lin\GuestbookBundle\Exception\InvalidFormException;

class GuestHandler implements GuestHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository('LinGuestbookBundle:Guest');
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Guest.
     *
     * @param mixed $id
     *
     * @return Guest
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Guests.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Guest.
     *
     * @param $request
     *
     * @return Guest
     */
    public function post($request)
    {
        $guest = $this->createGuest();

        return $this->processForm($guest, $request, 'POST');
    }

    /**
     * Edit a Guest.
     *
     * @param Guest $guest
     * @param $request
     *
     * @return Guest
     */
    public function put(Guest $guest, $request)
    {
        return $this->processForm($guest, $request, 'PUT');
    }

    /**
     * Partially update a Guest.
     *
     * @param Guest $guest
     * @param $request
     *
     * @return Guest
     */
    public function patch(Guest $guest, $request)
    {
        return $this->processForm($guest, $request, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param Guest $guest
     * @param $request
     * @param String        $method
     *
     * @return Guest
     *
     * @throws \Lin\GuestbookBundle\Exception\InvalidFormException
     */
    private function processForm(Guest $guest, $request, $method = "PUT")
    {
        $form = $this->formFactory->create(new GuestType(), $guest, array('method' => $method));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $guest->upload();
            $this->om->persist($guest);
            $this->om->flush($guest);
            return $guest;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createGuest()
    {
        return new $this->entityClass();
    }

}