<?php

namespace FFreitasBr\MandrillBundle\Integration\DependencyInjection;

use FFreitasBr\MandrillBundle\DependencyInjection\MandrillExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MandrillExtensionTest
 *
 * @package FFreitasBr\CommandLockBundle\Integration\DependencyInjection
 */
class MandrillExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var null|ContainerBuilder
     */
    protected static $container = null;

    /**
     * @var null|MandrillExtension
     */
    protected static $extension = null;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::$container = new ContainerBuilder();
        static::$extension = new MandrillExtension();
    }

    public function testMustTriggerExceptionWhenLoadBundleWithEmptyConfiguration()
    {
        $this->setExpectedException(
            '\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            "The child node \"dispatchers\" at path \"mandrill\" must be configured.",
            0
        );
        $configuration = array();
        static::$extension->load($configuration, static::$container);
    }

    public function testMustRegisterConfigurationsInContainerWithDefaultValues()
    {
        // must load without dispatchers node
        $configuration = array(
            0 => array(
                'api_key' => 'test_api_key',
            )
        );
        static::$extension->load($configuration, static::$container);
        $this->assertTrue(static::$container->hasParameter('mandrill.configuration'));
        $configurations = static::$container->getParameter('mandrill.configuration');
        $this->assertArrayHasKey('dispatchers', $configurations);
        $this->assertArrayHasKey('default', $configurations['dispatchers']);
        $this->assertArrayHasKey('api_key', $configurations['dispatchers']['default']);
        $this->assertEquals('test_api_key', $configurations['dispatchers']['default']['api_key']);

        // must load with dispatchers node
        $configuration = array(
            0 => array(
                'dispatchers' => array(
                    'api_key' => 'test_api_key_2',
                ),
            )
        );
        static::$extension->load($configuration, static::$container);
        static::$extension->load($configuration, static::$container);
        $this->assertTrue(static::$container->hasParameter('mandrill.configuration'));
        $configurations = static::$container->getParameter('mandrill.configuration');
        $this->assertArrayHasKey('dispatchers', $configurations);
        $this->assertArrayHasKey('default', $configurations['dispatchers']);
        $this->assertArrayHasKey('api_key', $configurations['dispatchers']['default']);
        $this->assertEquals('test_api_key_2', $configurations['dispatchers']['default']['api_key']);

        // check default values
        $defaultDispatcher = $configurations['dispatchers']['default'];
        $this->assertArrayHasKey('defaults', $defaultDispatcher);
        $this->assertArrayHasKey('disable_delivery', $defaultDispatcher);
        $this->assertArrayHasKey('proxy', $defaultDispatcher);
        $this->assertArrayHasKey('sender', $defaultDispatcher['defaults']);
        $this->assertEquals(null, $defaultDispatcher['defaults']['sender']);
        $this->assertArrayHasKey('sender_name', $defaultDispatcher['defaults']);
        $this->assertEquals(null, $defaultDispatcher['defaults']['sender_name']);
        $this->assertArrayHasKey('subaccount', $defaultDispatcher['defaults']);
        $this->assertEquals(null, $defaultDispatcher['defaults']['subaccount']);
        $this->assertFalse($defaultDispatcher['disable_delivery']);
        $this->assertArrayHasKey('use', $defaultDispatcher['proxy']);
        $this->assertFalse($defaultDispatcher['proxy']['use']);
        $this->assertArrayHasKey('host', $defaultDispatcher['proxy']);
        $this->assertEquals(null, $defaultDispatcher['proxy']['host']);
        $this->assertArrayHasKey('port', $defaultDispatcher['proxy']);
        $this->assertEquals(null, $defaultDispatcher['proxy']['port']);
        $this->assertArrayHasKey('user', $defaultDispatcher['proxy']);
        $this->assertEquals(null, $defaultDispatcher['proxy']['user']);
        $this->assertArrayHasKey('password', $defaultDispatcher['proxy']);
        $this->assertEquals(null, $defaultDispatcher['proxy']['password']);
    }

    public function testMustRegisterConfigurationsInContainerWithCompleteConfiguration()
    {
        // must load without dispatchers node
        $configuration = array(
            0 => array(
                'dispatchers' => array(
                    'default' => array(
                        'api_key' => 'test_api_key_1',
                        'disable_delivery' => true,
                        'defaults' => array(
                            'sender'      => 'sender1',
                            'sender_name' => 'sender1',
                        ),
                        'proxy' => array(
                            'use'      => true,
                            'host'     => 'host1',
                            'port'     => 'port1',
                            'user'     => 'user1',
                            'password' => 'password1',
                        ),
                    ),
                    'dispatcher1' => array(
                        'api_key' => 'test_api_key_2',
                        'disable_delivery' => false,
                        'defaults' => array(
                            'subaccount' => 'subaccount2',
                        ),
                        'proxy' => array(
                            'use'      => false,
                        ),
                    ),
                ),
            )
        );

        static::$extension->load($configuration, static::$container);
        $this->assertTrue(static::$container->hasParameter('mandrill.configuration'));
        $configurations = static::$container->getParameter('mandrill.configuration');

        $expected = array(
            'dispatchers' => array(
                'default' => array(
                    'api_key' => 'test_api_key_1',
                    'disable_delivery' => true,
                    'defaults' => array(
                        'sender'      => 'sender1',
                        'sender_name' => 'sender1',
                        'subaccount'  => null,
                    ),
                    'proxy' => array(
                        'use'      => true,
                        'host'     => 'host1',
                        'port'     => 'port1',
                        'user'     => 'user1',
                        'password' => 'password1',
                    ),
                ),
                'dispatcher1' => array(
                    'api_key' => 'test_api_key_2',
                    'disable_delivery' => false,
                    'defaults' => array(
                        'sender'      => null,
                        'sender_name' => null,
                        'subaccount'  => 'subaccount2',
                    ),
                    'proxy' => array(
                        'use'      => false,
                        'host'     => null,
                        'port'     => null,
                        'user'     => null,
                        'password' => null,
                    ),
                ),
            ),
        );
        $this->assertEquals($expected, $configurations);

        // check registered definitions
        $this->assertTrue(static::$container->hasAlias('mandrill.dispatcher'));
        $this->assertEquals('mandrill.dispatcher.default', static::$container->getAlias('mandrill.dispatcher'));
        $this->assertTrue(static::$container->hasDefinition('mandrill.dispatcher.default'));
        $this->assertTrue(static::$container->hasDefinition('mandrill.dispatcher.dispatcher1'));
    }

}
