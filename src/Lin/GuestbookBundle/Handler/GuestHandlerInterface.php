<?php

namespace Lin\GuestbookBundle\Handler;

use Lin\GuestbookBundle\Entity\Guest;

interface GuestHandlerInterface
{
    /**
     * Get a Guest given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return Guest
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
     * @param $request
     *
     * @return Guest
     */
    public function post($request);

    /**
     * Edit a Guest.
     *
     * @api
     *
     * @param Guest   $guest
     * @param $request
     *
     * @return Guest
     */
    public function put(Guest $guest, $request);

    /**
     * Partially update a Guest.
     *
     * @api
     *
     * @param Guest   $guest
     * @param $request
     *
     * @return Guest
     */
    public function patch(Guest $guest, $request);
}