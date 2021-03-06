<?php
/*
 * Copyright (c) 2011 Litle & Co.
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/
require_once realpath(dirname(__FILE__)) . '/../../lib/LitleOnline.php';

class captureGivenAuth_UnitTest extends PHPUnit_Framework_TestCase
{
	function test_simple_captureGivenAuth()
	{
		$hash_in = array(
       'amount'=>'123',
       'orderId'=>'12344',
       'authInformation' => array(
       'authDate'=>'2002-10-09','authCode'=>'543216',
       'authAmount'=>'12345'),
       'orderSource'=>'ecommerce',
       'card'=>array(
       'type'=>'VI',
       'number' =>'4100000000000001',
       'expDate' =>'1210'));
		$mock = $this->getMock('LitleXmlMapper');
		$mock	->expects($this->once())
		->method('request')
		->with($this->matchesRegularExpression('/.*<authInformation><authDate>2002-10-09.*<authCode>543216.*><authAmount>12345.*/'));
		
		$litleTest = new LitleOnlineRequest();
		$litleTest->newXML = $mock;
		$litleTest->captureGivenAuthRequest($hash_in);
		
	}

	function test_no_amount()
	{
		$hash_in = array(
       'reportGroup'=>'Planets',
       'orderId'=>'12344',
       'authInformation' => array(
       'authDate'=>'2002-10-09','authCode'=>'543216',
       'authAmount'=>'12345'),
       'orderSource'=>'ecommerce',
       'card'=>array(
       'type'=>'VI',
       'number' =>'4100000000000001',
       'expDate' =>'1210'));
		$litleTest = new LitleOnlineRequest();
		$this->setExpectedException("InvalidArgumentException","Missing Required Field: /amount/");
		$retOb = $litleTest->captureGivenAuthRequest($hash_in);
	}

	function test_both_choices_card_and_token()
	{
		$hash_in = array(
      	'reportGroup'=>'Planets',
      	'orderId'=>'1234',
     	'amount'=>'106',
     	'orderSource'=>'ecommerce',
      	'authInformation' => array(
      	'authDate'=>'2002-10-09','authCode'=>'543216',
     	'authAmount'=>'12345'),
      	'token'=> array(
      	'litleToken'=>'123456789101112',
     	'expDate'=>'1210',
    	'cardValidationNum'=>'555',
    	'type'=>'VI'),
     	'card'=>array(
    	'type'=>'VI',
    	'number' =>'4100000000000001',
    	'expDate' =>'1210'));
		$litleTest = new LitleOnlineRequest();
		$this->setExpectedException('InvalidArgumentException',"Entered an Invalid Amount of Choices for a Field, please only fill out one Choice!!!!");
		$retOb = $litleTest->captureGivenAuthRequest($hash_in);
	}
	
	function test_all_choices()
	{
		$hash_in = array(
			      'reportGroup'=>'Planets',
			      'litleTxnId'=>'123456',
			      'orderId'=>'12344',
			      'amount'=>'106',
			      'orderSource'=>'ecommerce',
			      'fraudCheck'=>array('authenticationTransactionId'=>'123'),
			      'cardholderAuthentication'=>array('authenticationTransactionId'=>'123'),
			      'card'=>array(
			      'type'=>'VI',
			      'number' =>'4100000000000001',
			      'expDate' =>'1210'),
				  'paypage'=> array(
				  'paypageRegistrationId'=>'1234',
				  'expDate'=>'1210',
				  'cardValidationNum'=>'555',
				  'type'=>'VI'),
			      'token'=> array(
			      'litleToken'=>'1234',
			      'expDate'=>'1210',
			      'cardValidationNum'=>'555',
			      'type'=>'VI'));
		$litleTest = new LitleOnlineRequest();
		$this->setExpectedException('InvalidArgumentException',"Entered an Invalid Amount of Choices for a Field, please only fill out one Choice!!!!");
		$retOb = $litleTest->captureGivenAuthRequest($hash_in);
	}
	
	function test_loggedInUser()
	{
		$hash_in = array(
				'loggedInUser'=>'gdake',
 				'merchantSdk'=>'PHP;8.14.0',
				'amount'=>'123',
				'orderId'=>'12344',
				'authInformation' => array(
						'authDate'=>'2002-10-09','authCode'=>'543216',
						'authAmount'=>'12345'),
				'orderSource'=>'ecommerce',
				'card'=>array(
						'type'=>'VI',
						'number' =>'4100000000000001',
						'expDate' =>'1210'));
		$mock = $this->getMock('LitleXmlMapper');
		$mock	->expects($this->once())
		->method('request')
 		->with($this->matchesRegularExpression('/.*merchantSdk="PHP;8.14.0".*loggedInUser="gdake" xmlns=.*>.*/'));
			
		$litleTest = new LitleOnlineRequest();
		$litleTest->newXML = $mock;
		$litleTest->captureGivenAuthRequest($hash_in);
	
	}
	
	function test_surchargeAmount() {
 		$hash_in = array(
 			'amount'=>'2',
 			'surchargeAmount'=>'1',
 			'orderSource'=>'ecommerce',
 			'orderId'=>'3'
 		);
 		$mock = $this->getMock('LitleXmlMapper');
 		$mock
 			->expects($this->once())
 			->method('request')
 			->with($this->matchesRegularExpression('/.*<amount>2<\/amount><surchargeAmount>1<\/surchargeAmount><orderSource>ecommerce<\/orderSource>.*/'));
 			
 		$litleTest = new LitleOnlineRequest();
 		$litleTest->newXML = $mock;
 		$litleTest->captureGivenAuthRequest($hash_in);
 	}
 	
 	function test_surchargeAmount_optional() {
 		$hash_in = array(
 				'amount'=>'2',
 				'orderSource'=>'ecommerce',
 				'orderId'=>'3'
 		);
 		$mock = $this->getMock('LitleXmlMapper');
 		$mock
 		->expects($this->once())
 		->method('request')
 		->with($this->matchesRegularExpression('/.*<amount>2<\/amount><orderSource>ecommerce<\/orderSource>.*/'));
 	
 		$litleTest = new LitleOnlineRequest();
 		$litleTest->newXML = $mock;
 		$litleTest->captureGivenAuthRequest($hash_in);
 	}
}
