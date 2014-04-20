<?php

namespace Lin\GuestbookBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Lin\GuestbookBundle\Model\GuestInterface;
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
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Guest.
     *
     * @param mixed $id
     *
     * @return GuestInterface
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
     * @param array $parameters
     *
     * @return GuestInterface
     */
    public function post(array $parameters)
    {
        $guest = $this->createGuest();

        return $this->processForm($guest, $parameters, 'POST');
    }

    /**
     * Edit a Page.
     *
     * @param GuestInterface $guest
     * @param array         $parameters
     *
     * @return GuestInterface
     */
    public function put(GuestInterface $guest, array $parameters)
    {
        return $this->processForm($guest, $parameters, 'PUT');
    }

    /**
     * Partially update a Guest.
     *
     * @param GuestInterface $guest
     * @param array         $parameters
     *
     * @return GuestInterface
     */
    public function patch(GuestInterface $guest, array $parameters)
    {
        return $this->processForm($guest, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param GuestInterface $guest
     * @param array         $parameters
     * @param String        $method
     *
     * @return GuestInterface
     *
     * @throws \Lin\GuestbookBundle\Exception\InvalidFormException
     */
    private function processForm(GuestInterface $page, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new GuestType(), $page, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $guest = $form->getData();
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