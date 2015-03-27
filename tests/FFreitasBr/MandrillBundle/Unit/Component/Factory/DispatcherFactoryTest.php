<?php

namespace FFreitasBr\MandrillBundle\Unit\Component\Message;

use FFreitasBr\MandrillBundle\Component\Factory\DispatcherFactory;
use FFreitasBr\MandrillBundle\Component\Message\Message;
use FFreitasBr\MandrillBundle\Service\Dispatcher;
use Symfony\Component\DependencyInjection\Container;

/**
 * Dispatcher Factory Tests
 */
class DispatcherFactoryTest extends \PHPUnit_Framework_TestCase
{

    protected function getProperty($class, $reflection, $property)
    {
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($class);
    }

    public function testIfCreateACorrectDispatcherObject()
    {
        /* @var Dispatcher $dispatcher */
        $dispatcher = DispatcherFactory::create(new Container(), 'Mandrill', 'test_name', array(
            'api_key' => 'test_api_key',
            'disable_delivery' => true,
            'defaults' => array(
                'sender'      => 'sender_test',
                'sender_name' => 'sender_name_test',
                'subaccount'  => 'subaccount_test',
            ),
            'proxy' => array(
                'use'      => true,
                'host'     => 'host_test',
                'port'     => 'port_test',
                'user'     => 'user_test',
                'password' => 'password_test',
            ),
        ));

        // test dispatcher
        $this->assertInstanceOf('FFreitasBr\MandrillBundle\Service\Dispatcher', $dispatcher);

        // create reflection
        $reflectionOfDispatcher = new \ReflectionClass($dispatcher);

        // test service
        $service = $this->getProperty($dispatcher, $reflectionOfDispatcher, 'service');
        $this->assertInstanceOf('Mandrill', $service);

        // test dispatcher name
        $this->assertEquals('test_name', $this->getProperty($dispatcher, $reflectionOfDispatcher, 'myName'));

        // test disable_delivery
        $this->assertTrue($this->getProperty($dispatcher, $reflectionOfDispatcher, 'disableDelivery'));

        // test proxy
        $proxy = $this->getProperty($dispatcher, $reflectionOfDispatcher, 'proxy');
        $this->assertContains('use', $proxy);
        $this->assertTrue($proxy['use']);
        $this->assertContains('host', $proxy);
        $this->assertEquals('host_test', $proxy['host']);
        $this->assertContains('port', $proxy);
        $this->assertEquals('port_test', $proxy['port']);
        $this->assertContains('user', $proxy);
        $this->assertEquals('user_test', $proxy['user']);
        $this->assertContains('password', $proxy);
        $this->assertEquals('password_test', $proxy['password']);

        // test defaults
        $this->assertEquals('sender_test', $this->getProperty($dispatcher, $reflectionOfDispatcher, 'defaultSender'));
        $this->assertEquals('sender_name_test', $this->getProperty($dispatcher, $reflectionOfDispatcher, 'defaultSenderName'));
        $this->assertEquals('subaccount_test', $this->getProperty($dispatcher, $reflectionOfDispatcher, 'subAccount'));
    }

    public function testIfCreateACorrectDispatcherObjectWithEmptyName()
    {
        /* @var Dispatcher $dispatcher */
        $dispatcher = DispatcherFactory::create(new Container(), 'Mandrill', null, array(
            'api_key' => 'test_api_key',
            'disable_delivery' => true,
            'defaults' => array(
                'sender'      => 'sender_test',
                'sender_name' => 'sender_name_test',
                'subaccount'  => 'subaccount_test',
            ),
            'proxy' => array(
                'use'      => true,
                'host'     => 'host_test',
                'port'     => 'port_test',
                'user'     => 'user_test',
                'password' => 'password_test',
            ),
        ));

        // create reflection
        $reflectionOfDispatcher = new \ReflectionClass($dispatcher);

        // test dispatcher name
        $this->assertEquals('default', $this->getProperty($dispatcher, $reflectionOfDispatcher, 'myName'));
    }
}