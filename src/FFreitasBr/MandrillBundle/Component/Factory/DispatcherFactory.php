<?php

namespace FFreitasBr\MandrillBundle\Component\Factory;

use FFreitasBr\MandrillBundle\Service\Dispatcher;
use Mandrill;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DispatcherFactory
 *
 * @package FFreitasBr\MandrillBundle\Component\Factory
 */
class DispatcherFactory
{

    /**
     * Static constructor
     * Used in the service container factory
     *
     * @param ContainerInterface   $container
     * @param                      $mandrillClass
     * @param string               $dispatcherName
     * @param array                $dispatcherConfig
     *
     * @return Dispatcher
     */
    public static function create(ContainerInterface $container, $mandrillClass, $dispatcherName, array $dispatcherConfig)
    {
        // get dispatcher name
        if (!isset($dispatcherName) || empty($dispatcherName)) {
            $dispatcherName = 'default';
        }

        // create a new instance of Mandrill class
        /* @var Mandrill $mandrillService */
        $mandrillService = new $mandrillClass($dispatcherConfig['api_key']);

        // get defaults configuration
        $defaultSender     = (isset($dispatcherConfig['defaults']['sender']))      ? $dispatcherConfig['defaults']['sender']      : null;
        $defaultSenderName = (isset($dispatcherConfig['defaults']['sender_name'])) ? $dispatcherConfig['defaults']['sender_name'] : null;
        $subAccount        = (isset($dispatcherConfig['defaults']['subaccount']))  ? $dispatcherConfig['defaults']['subaccount']  : null;

        // get disable delivery configuration
        $disableDelivery = $dispatcherConfig['disable_delivery'];

        // get proxy configuration
        $proxy = (isset($dispatcherConfig['proxy'])) ? $dispatcherConfig['proxy'] : array('use' => false);

        // create and return the new dispatcher
        return new Dispatcher(
            $dispatcherName,
            $mandrillService,
            $defaultSender,
            $defaultSenderName,
            $subAccount,
            $disableDelivery,
            $proxy
        );
    }
}
