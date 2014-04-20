<?php

namespace Lin\GuestbookBundle\Model;

Interface GuestInterface
{
    /**
     * Set title
     *
     * @param string $title
     * @return GuestInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return GuestInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();
}
