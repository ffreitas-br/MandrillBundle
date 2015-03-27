<?php

namespace FFreitasBr\MandrillBundle\Service;

use FFreitasBr\MandrillBundle\Component\Message\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Dispatcher
 *
 * @package FFreitasBr\MandrillBundle\Service
 */
class Dispatcher
{
    /**
     * This dispatcher name
     *
     * @var string
     */
    protected $myName = 'default';

    /**
     * Mandrill service
     *
     * @var \Mandrill $service
     */
    protected $service;

    /**
     * Default Sender Email
     *
     * @var string
     */
    protected $defaultSender;

    /**
     * Default subAccount
     *
     * @var string
     */
    protected $subAccount;

    /**
     * Default Sender Name
     *
     * @var string
     */
    protected $defaultSenderName;

    /**
     * Proxy options
     *
     * @var array
     */
    protected $proxy;

    /**
     * @var bool
     */
    protected $disableDelivery;

    /**
     * @param $dispatcherName
     * @param $service
     * @param $defaultSender
     * @param $defaultSenderName
     * @param $subAccount
     * @param $disableDelivery
     * @param $proxy
     *
     * @return Dispatcher
     */
    public function __construct($dispatcherName, $service, $defaultSender, $defaultSenderName, $subAccount, $disableDelivery, $proxy) {
        $this->myName            = $dispatcherName;
        $this->service           = $service;
        $this->defaultSender     = $defaultSender;
        $this->defaultSenderName = $defaultSenderName;
        $this->subAccount        = $subAccount;
        $this->disableDelivery   = $disableDelivery;
        $this->proxy             = $proxy;
        if ($this->useProxy()) {
            $this->addCurlProxyOptions();
        }
    }

    /**
     * Send a message
     *
     * @param Message $message
     * @param string $templateName
     * @param array $templateContent
     * @param bool $async
     * @param string $ipPool
     * @param string $sendAt
     *
     * @return array|bool
     */
    public function send(Message $message, $templateName = '', $templateContent = array(), $async = false, $ipPool=null, $sendAt=null)
    {
        if ($this->disableDelivery) {
            return false;
        }

        if (strlen($message->getFromEmail()) == 0 && null !== $this->defaultSender) {
            $message->setFromEmail($this->defaultSender);
        }

        if (strlen($message->getFromName()) == 0 && null !== $this->defaultSenderName) {
            $message->setFromName($this->defaultSenderName);
        }

        if (strlen($message->getSubaccount()) == 0 && null !== $this->subAccount) {
            $message->setSubaccount($this->subAccount);
        }

        if (!empty($templateName)) {
            return $this->service->messages->sendTemplate($templateName, $templateContent, $message->toArray(), $async, $ipPool, $sendAt);
        }

        return $this->service->messages->send($message->toArray(), $async, $ipPool, $sendAt);
    }

    protected function useProxy()
    {
        return $this->proxy['use'];
    }

    protected function addCurlProxyOptions()
    {
        if ($this->proxy['host'] !== null) {
            curl_setopt($this->service->ch, CURLOPT_PROXY, $this->proxy['host']);
        }
        
        if ($this->proxy['port'] !== null) {
            curl_setopt($this->service->ch, CURLOPT_PROXYPORT, $this->proxy['port']);
        }
        
        if ($this->proxy['user'] !== null && $this->proxy['password'] !== null) {
            curl_setopt($this->service->ch, CURLOPT_PROXYUSERPWD, sprintf(
                '%s:%s',
                $this->proxy['user'],
                $this->proxy['password']
            ));
        }
    }
}
