<?php

namespace FFreitasBr\MandrillBundle\Integration\Fixtures\Mock;

use FFreitasBr\MandrillBundle\Component\Message\Message;

/**
 * Class MandrillMessagesMock
 *
 * @package FFreitasBr\MandrillBundle\Integration\Fixtures\Mock
 */
class MandrillMessagesMock extends \Mandrill_Messages
{

    /**
     * @var null|Message
     */
    public $sentMessage = null;
    public function send($message, $async=false, $ip_pool=null, $send_at=null) {
        $this->sentMessage = $message;
        return null;
    }

    public $sentTemplateName    = null;
    public $sentTemplateContent = null;
    public $sentTemplateMessage = null;
    public function sendTemplate($template_name, $template_content, $message, $async=false, $ip_pool=null, $send_at=null) {
        $this->sentTemplateName    = $template_name;
        $this->sentTemplateContent = $template_content;
        $this->sentTemplateMessage = $message;
        return null;
    }
}