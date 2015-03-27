<?php

namespace FFreitasBr\MandrillBundle\Unit\Component\Message;

use FFreitasBr\MandrillBundle\Component\Message\Message;

/**
 * Message Tests
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    public function testToIsInitializedAsArray()
    {
        $message = new Message();
        $recipientArray = $message->getTo();

        $this->assertTrue(is_array($recipientArray));
    }

    public function testAddToWithoutName()
    {
        $message = new Message();
        $message->addTo('test@email.com');
        $recipientArray = $message->getTo();

        $this->assertTrue(is_array($recipientArray));
        $this->assertEquals(count($recipientArray), 1);
        $this->assertArrayHasKey('email', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['email'], 'test@email.com');
        $this->assertArrayHasKey('name', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['name'], '');
    }

    public function testAddToWithName() {
        $message = new Message();
        $message->addTo('test@email.com', 'Abc Def');
        $recipientArray = $message->getTo();

        $this->assertTrue(is_array($recipientArray));
        $this->assertEquals(count($recipientArray), 1);
        $this->assertArrayHasKey('email', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['email'], 'test@email.com');
        $this->assertArrayHasKey('name', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['name'], 'Abc Def');
    }

    public function testAddToWith2Recipients() {
        $message = new Message();
        $message->addTo('test@email.com', 'Abc Def');
        $message->addTo('test2@email.com', 'Foo Bar');
        $recipientArray = $message->getTo();

        $this->assertTrue(is_array($recipientArray));
        $this->assertEquals(count($recipientArray), 2);
        $this->assertArrayHasKey('email', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['email'], 'test@email.com');
        $this->assertArrayHasKey('name', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['name'], 'Abc Def');

        $this->assertArrayHasKey('email', $recipientArray[1]);
        $this->assertEquals($recipientArray[1]['email'], 'test2@email.com');
        $this->assertArrayHasKey('name', $recipientArray[1]);
        $this->assertEquals($recipientArray[1]['name'], 'Foo Bar');
    }

    public function testAddToWithTypes()
    {
        $message = new Message();
        $message->addTo('to-test@example.com', 'Foo Bar');
        $message->addTo('cc-test@example.com', 'Foo User', 'cc');
        $message->addTo('bcc-test@example.com', 'Bar User', 'bcc');
        $recipientArray = $message->getTo();

        $this->assertTrue(is_array($recipientArray));
        $this->assertEquals(count($recipientArray), 3);
        $this->assertArrayHasKey('type', $recipientArray[0]);
        $this->assertEquals($recipientArray[0]['type'], 'to');
        $this->assertArrayHasKey('type', $recipientArray[1]);
        $this->assertEquals($recipientArray[1]['type'], 'cc');
        $this->assertArrayHasKey('type', $recipientArray[2]);
        $this->assertEquals($recipientArray[2]['type'], 'bcc');
    }

    public function testHeaderIsInitialized()
    {
        $message = new Message();

        $this->assertTrue(is_array($message->getHeaders()));
    }

    public function testAddReplyToHeader()
    {

        $message = new Message();

        $message->addHeader('Reply-To', 'test@email.com');

        $headers = $message->getHeaders();

        $this->assertTrue(is_array($headers));
        $this->assertEquals($headers['Reply-To'], 'test@email.com');

    }

    public function testAddXHeader()
    {
        $message = new Message();

        $message->addHeader('X-Binford', 'more power (9100)');

        $headers = $message->getHeaders();

        $this->assertTrue(is_array($headers));
        $this->assertEquals($headers['X-Binford'], 'more power (9100)');

    }

    public function testSetSubaccount()
    {
        $message = new Message();

        $message->setSubaccount('Subaccount Name');

        $this->assertEquals($message->getSubaccount(), 'Subaccount Name');
    }
    
    public function testAddImage()
    {
        $message = new Message();
        
        $message->addImage('image/jpg', '1x1', '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFAEBAAAAAAAAAAAAAAAAAAAAAP/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AL+AD//Z');
        
        $images = $message->getImages();
        
        $this->assertEquals(1, count($images));
        $this->assertEquals($images[0]['name'], '1x1');
    }

    public function testSetReplyTo()
    {
        $testString = "foo@bar.baz";

        $message = new Message();

        $message->setReplyTo($testString);
        $headers = $message->getHeaders();
        $this->assertEquals($testString, $headers['Reply-To']);
    }

    public function testIsImportant()
    {
        $message = new Message();
        $message->isImportant();
        $headers = $message->getHeaders();
        $this->assertEquals('High', $headers['Importance']);
        $this->assertEquals('urgent', $headers['Priority']);
    }

    public function testSetMergeVar()
    {
        $message = new Message();
        $this->assertEquals(false, $message->getMerge());
        $message->addMergeVar('test@foo.com', 'testkey', 'testvalue');
        $this->assertEquals(true, $message->getMerge());
    }

    public function testSetGlobalMergeVar()
    {
        $message = new Message();
        $this->assertEquals(false, $message->getMerge());
        $message->addGlobalMergeVar('testkey', 'testvalue');
        $this->assertEquals(true, $message->getMerge());
        $expected = array(
            0 => array(
                'name'    => 'testkey',
                'content' => 'testvalue',
            ),
        );
        $this->assertEquals($expected, $message->getGlobalMergeVars());
    }

    public function testAddmergevarsAndGetmergevars()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getMergeVars());

        $message->addMergeVars('recipient1', array('key1' => 'value1', 'key2' => 'value2'));
        $expected1 = array(
            0 => array(
                'rcpt' => 'recipient1',
                'vars' => array(
                    0 => array(
                        'name'    => 'key1',
                        'content' => 'value1',
                    ),
                    1 => array(
                        'name'    => 'key2',
                        'content' => 'value2',
                    ),
                ),
            ),
        );
        $this->assertEquals($expected1, $message->getMergeVars());

        $message->addMergeVars('recipient2', array('key3' => 'value3', 'key4' => 'value4'));
        $expected2 = array(
            0 => array(
                'rcpt' => 'recipient1',
                'vars' => array(
                    0 => array(
                        'name'    => 'key1',
                        'content' => 'value1',
                    ),
                    1 => array(
                        'name'    => 'key2',
                        'content' => 'value2',
                    ),
                ),
            ),
            1 => array(
                'rcpt' => 'recipient2',
                'vars' => array(
                    0 => array(
                        'name'    => 'key3',
                        'content' => 'value3',
                    ),
                    1 => array(
                        'name'    => 'key4',
                        'content' => 'value4',
                    ),
                ),
            ),
        );
        $this->assertEquals($expected2, $message->getMergeVars());
    }

    public function testAddtagAndGettags()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getTags());
        $message->addTag('test1');
        $this->assertEquals(array(0 => 'test1'), $message->getTags());
        $message->addTag('test2');
        $this->assertEquals(array(0 => 'test1', 1 => 'test2'), $message->getTags());
    }

    public function testAddTagMustThrowException()
    {
        $this->setExpectedException('Exception', 'tag cannot start with underscore');
        $message = new Message();
        $message->addTag('_invalid');
    }

    public function testSettextAndGettext()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getText());
        $message->setText('text_test');
        $this->assertEquals('text_test', $message->getText());
    }

    public function testSethtmlAndGethtml()
    {
        $message = new Message();
        $this->assertEquals('', $message->getHtml());
        $message->setHtml('html_test');
        $this->assertEquals('html_test', $message->getHtml());
    }

    public function testSetsubjectAndGetsubject()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getSubject());
        $message->setSubject('subject_test');
        $this->assertEquals('subject_test', $message->getSubject());
    }

    public function testAddmetadataAndGetmetadata()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getMetadata());

        $metadata = array(
            'key1' => 'value1',
            'key3' => 'value3',
        );
        $message->addMetadata($metadata);
        $this->assertEquals($metadata, $message->getMetadata());

        $expected = array(
            'key1' => 'value1',
            'key3' => 'value3',
            0      => 'value4',
        );
        $message->addMetadata('value4');
        $this->assertEquals($expected, $message->getMetadata());

        $expected = array(
            'key1' => 'value1',
            'key3' => 'value3',
            0      => 'value4',
            1      => 'value2',
        );
        $message->addMetadata('value2');
        $this->assertEquals($expected, $message->getMetadata());
    }

    public function testSetbccaddressAndGetbccaddress()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getBccAddress());
        $message->setBccAddress('bcc_address_test');
        $this->assertEquals('bcc_address_test', $message->getBccAddress());
    }

    public function testSetfromemailAndGetfromemail()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getFromEmail());
        $message->setFromEmail('from_email_test');
        $this->assertEquals('from_email_test', $message->getFromEmail());
    }

    public function testSetfromnameAndGetfromname()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getFromName());
        $message->setFromName('from_name_test');
        $this->assertEquals('from_name_test', $message->getFromName());
    }

    public function testSetinlinecssAndGetinlinecss()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getInlineCss());
        $message->setInlineCss('inline_css_test');
        $this->assertEquals('inline_css_test', $message->getInlineCss());
    }

    public function testSetreturnpathdomainAndGetreturnpathdomain()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getReturnPathDomain());
        $message->setReturnPathDomain('return_path_domain_test');
        $this->assertEquals('return_path_domain_test', $message->getReturnPathDomain());
    }

    public function testSetgoogleanalyticscampaignAndGetgoogleanalyticscampaign()
    {
        $message = new Message();
        $this->assertEquals('', $message->getGoogleAnalyticsCampaign());
        $message->setGoogleAnalyticsCampaign('google_analytics_domain_test');
        $this->assertEquals('google_analytics_domain_test', $message->getGoogleAnalyticsCampaign());
    }

    public function testSetautotextAndGetautotext()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getAutoText());
        $message->setAutoText(true);
        $this->assertTrue($message->getAutoText());
    }

    public function testSetautohtmlAndGetautohtml()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getAutoHtml());
        $message->setAutoHtml(true);
        $this->assertTrue($message->getAutoHtml());
    }

    public function testAddgoogleanalyticsdomainAndGetgoogleanaluticsdomain()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getGoogleAnalyticsDomains());

        $expected1 = array(
            0 => 'domain_1',
        );
        $message->addGoogleAnalyticsDomain('domain_1');
        $this->assertEquals($expected1, $message->getGoogleAnalyticsDomains());

        $expected2 = array(
            0 => 'domain_1',
            1 => 'domain_2',
        );
        $message->addGoogleAnalyticsDomain('domain_2');
        $this->assertEquals($expected2, $message->getGoogleAnalyticsDomains());
    }

    public function testAddrecipientmetadataAndGetrecipientmetadata()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getRecipientMetadata());

        $expected1 = array(
            0 => array(
                'rcpt' => 'recipient_1',
                'values' => array(
                    'value_test_1',
                )
            ),
        );
        $message->addRecipientMetadata('recipient_1', 'value_test_1');
        $this->assertEquals($expected1, $message->getRecipientMetadata());

        $expected2 = array(
            0 => array(
                'rcpt' => 'recipient_1',
                'values' => array(
                    'value_test_1',
                )
            ),
            1 => array(
                'rcpt' => 'recipient_2',
                'values' => array(
                    'value_test_2',
                    'value_test_3',
                )
            ),
        );
        $message->addRecipientMetadata('recipient_2', array('value_test_2', 'value_test_3'));
        $this->assertEquals($expected2, $message->getRecipientMetadata());

        $expected3 = array(
            0 => array(
                'rcpt' => 'recipient_1',
                'values' => array(
                    'value_test_2',
                    'value_test_3',
                )
            ),
            1 => array(
                'rcpt' => 'recipient_2',
                'values' => array(
                    'value_test_2',
                    'value_test_3',
                    'value_test_1',
                )
            ),
        );
        $message->addRecipientMetadata('recipient_2', 'value_test_1');
        $message->addRecipientMetadata('recipient_1', array('value_test_2', 'value_test_3'));
        $this->assertEquals($expected3, $message->getRecipientMetadata());
    }

    public function testAddattachmentAndGetattachments()
    {
        $message = new Message();
        $this->assertEquals(array(), $message->getAttachments());

        $expected1 = array(
            0 => array(
                'type'    => 'type_1',
                'name'    => 'name_1',
                'content' => 'data_1'
            ),
        );
        $message->addAttachment('type_1', 'name_1', 'data_1');
        $this->assertEquals($expected1, $message->getAttachments());

        $expected2 = array(
            0 => array(
                'type'    => 'type_1',
                'name'    => 'name_1',
                'content' => 'data_1'
            ),
            1 => array(
                'type'    => 'type_2',
                'name'    => 'name_2',
                'content' => 'data_2'
            ),
        );
        $message->addAttachment('type_2', 'name_2', 'data_2');
        $this->assertEquals($expected2, $message->getAttachments());
    }

    public function testAddattachmentfrompathMustThrowException()
    {
        $this->setExpectedException('Exception', 'cannot read file /nothing');
        $message = new Message();
        $message->addAttachmentFromPath('/nothing');
    }

    public function testAddattachmentfrompath()
    {
        $message = new Message();

        $tmpFile = tmpfile();
        fwrite($tmpFile, 'data_test');
        $tmpFileMetadata = stream_get_meta_data($tmpFile);
        $filePath = $tmpFileMetadata['uri'];
        $fileName = basename($filePath);
        $base64data = base64_encode('data_test');

        // test without name
        $expected1 = array(
            0 => array(
                'type'    => 'type_test',
                'name'    => $fileName,
                'content' => $base64data
            ),
            1 => array(
                'type'    => 'type_test',
                'name'    => 'name_test',
                'content' => $base64data
            ),
        );
        $message->addAttachmentFromPath($filePath, 'type_test');
        $message->addAttachmentFromPath($filePath, 'type_test', 'name_test');
        $this->assertEquals($expected1, $message->getAttachments());
    }

    public function testAddimagefrompath()
    {
        $message = new Message();

        $tmpFile = tmpfile();
        fwrite($tmpFile, 'data_test');
        $tmpFileMetadata = stream_get_meta_data($tmpFile);
        $filePath = $tmpFileMetadata['uri'];
        $fileName = basename($filePath);
        $base64data = base64_encode('data_test');

        // test without name
        $expected1 = array(
            0 => array(
                'type'    => 'type_test',
                'name'    => $fileName,
                'content' => $base64data
            ),
            1 => array(
                'type'    => 'type_test',
                'name'    => 'name_test',
                'content' => $base64data
            ),
        );
        $message->addImageFromPath($filePath, 'type_test');
        $message->addImageFromPath($filePath, 'type_test', 'name_test');
        $this->assertEquals($expected1, $message->getImages());
    }

    public function testSettrackclicksAndGettrackclicks()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getTrackClicks());
        $message->setTrackClicks(true);
        $this->assertTrue($message->getTrackClicks());
    }

    public function testSettrackopensAndGettrackopens()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getTrackOpens());
        $message->setTrackOpens(true);
        $this->assertTrue($message->getTrackOpens());
    }

    public function testSeturlstripqsAndGeturlstripqs()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getUrlStripQs());
        $message->setUrlStripQs(true);
        $this->assertTrue($message->getUrlStripQs());
    }

    public function testSetpreserverecipientsAndGetpreserverecipients()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getPreserveRecipients());
        $message->setPreserveRecipients(true);
        $this->assertTrue($message->getPreserveRecipients());
    }

    public function testSetsigningdomainAndGetsigingdomain()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getSigningDomain());
        $message->setSigningDomain('signing_domain_test');
        $this->assertEquals('signing_domain_test', $message->getSigningDomain());
    }

    public function testSettrackingdomainAndGettrackingdomain()
    {
        $message = new Message();
        $this->assertEquals(null, $message->getTrackingDomain());
        $message->setTrackingDomain('tracking_domain_test');
        $this->assertEquals('tracking_domain_test', $message->getTrackingDomain());
    }

    public function testSetviewcontentlinkAndIsviewcontentlink()
    {
        $message = new Message();
        $this->assertEquals(null, $message->isViewContentLink());
        $message->setViewContentLink(true);
        $this->assertTrue($message->isViewContentLink());
    }

    public function testSetmergelanguageAndGetmergelanguage()
    {
        $message = new Message();
        $this->assertEquals('mailchimp', $message->getMergeLanguage());
        $message->setMergeLanguage('merge_language_test');
        $this->assertEquals('merge_language_test', $message->getMergeLanguage());
    }
}