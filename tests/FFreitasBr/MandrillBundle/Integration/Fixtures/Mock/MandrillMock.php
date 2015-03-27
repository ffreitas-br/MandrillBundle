<?php

namespace FFreitasBr\MandrillBundle\Integration\Fixtures\Mock;

/**
 * Class MandrillMock
 *
 * @package FFreitasBr\MandrillBundle\Integration\Fixtures\Mock
 */
class MandrillMock extends \Mandrill
{

    public function __construct($apikey=null) {
        parent::__construct($apikey);
        $this->messages = new MandrillMessagesMock($this);
    }
}