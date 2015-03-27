<?php

namespace FFreitasBr\MandrillBundle\Unit\Component\Message;

use FFreitasBr\MandrillBundle\Component\Message\Message;
use FFreitasBr\MandrillBundle\Integration\Fixtures\Mock\MandrillMessagesMock;
use FFreitasBr\MandrillBundle\Integration\Fixtures\Mock\MandrillMock;
use FFreitasBr\MandrillBundle\Service\Dispatcher;

/**
 * Dispatcher Tests
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testTestDisableDelivery()
    {
        $dispatcher = new Dispatcher(null, null, null, null, null, true, null);
        $message = new Message();
        $returnOfCall = $dispatcher->send($message);
        $this->assertFalse($returnOfCall);
    }

    public function testTestSendMessageWithoutTemplate()
    {
        $mandrillService  = new MandrillMock('api_key_test');
        /* @var MandrillMessagesMock $mandrillMessages */
        $mandrillMessages = $mandrillService->messages;

        // test with default values
        $dispatcher = new Dispatcher(
            'test_name',
            $mandrillService,
            'default_sender',
            'default_sender_name',
            'sub_account',
            false,
            null
        );
        $message = new Message();
        $dispatcher->send($message);
        $this->assertEquals('api_key_test', $mandrillService->apikey);
        $this->assertEquals('default_sender', $mandrillMessages->sentMessage['from_email']);
        $this->assertEquals('default_sender_name', $mandrillMessages->sentMessage['from_name']);
        $this->assertEquals('sub_account', $mandrillMessages->sentMessage['subaccount']);
    }

    public function testTestSendMessageWithTemplate()
    {
        $mandrillService  = new MandrillMock('api_key_test');
        /* @var MandrillMessagesMock $mandrillMessages */
        $mandrillMessages = $mandrillService->messages;

        // test with default values
        $dispatcher = new Dispatcher(
            'test_name',
            $mandrillService,
            'default_sender',
            'default_sender_name',
            'sub_account',
            false,
            null
        );
        $message = (new Message)
            ->setFromEmail('email1')
            ->setFromName('name1')
            ->setSubaccount('subaccount1')
        ;

        $dispatcher->send($message, 'template_name', array('template_data'));
        $this->assertEquals('api_key_test', $mandrillService->apikey);
        $this->assertEquals('email1', $mandrillMessages->sentTemplateMessage['from_email']);
        $this->assertEquals('name1', $mandrillMessages->sentTemplateMessage['from_name']);
        $this->assertEquals('subaccount1', $mandrillMessages->sentTemplateMessage['subaccount']);
        $this->assertEquals('template_name', $mandrillMessages->sentTemplateName);
        $this->assertEquals(array('template_data'), $mandrillMessages->sentTemplateContent);
    }
}