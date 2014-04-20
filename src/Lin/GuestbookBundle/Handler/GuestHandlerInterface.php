<?php

namespace Lin\GuestbookBundle\Handler;

use Lin\GuestbookBundle\Model\GuestInterface;

interface GuestHandlerInterface
{
    /**
     * Get a Guest given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return GuestInterface
     */
    public function get($id);

    /**
     * Get a list of Guests.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Guest, creates a new Guest.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return GuestInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Guest.
     *
     * @api
     *
     * @param GuestInterface   $guest
     * @param array           $parameters
     *
     * @return GuestInterface
     */
    public function put(GuestInterface $guest, array $parameters);

    /**
     * Partially update a Guest.
     *
     * @api
     *
     * @param GuestInterface   $guest
     * @param array           $parameters
     *
     * @return GuestInterface
     */
    public function patch(GuestInterface $guest, array $parameters);
}