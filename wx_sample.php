<?php
/**
  * wechat php test
  */
define('__ROOT__', dirname(__FILE__));
include("wechatMessage.php");
require_once(__ROOT__ . '/wechatMessage.php'); 
date_default_timezone_set("Asia/Singapore");
//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$MsgType = trim($postObj->MsgType);
				switch ($MsgType)
				{
					case "event":
						$result = $this->handleEvent($postObj);
						break;
					case "text":
						$result = $this->handleText($postObj);
						break;
					case "location":
						$storeLoc = array();
						$storeLoc[0] = array();
						$storeLoc[1] = array();
						$storeLoc[2] = array();
						$storeLoc[0][0] = 1.3397;
						$storeLoc[0][1] = 103.7054;
						$storeLoc[1][0] = 1.4301;
						$storeLoc[1][1] = 103.8356;
						$storeLoc[2][0] = 1.286;
						$storeLoc[2][1] = 103.868;
						$distance = array();
						$x = (float) $postObj->Location_X;
						$y = (float) $postObj->Location_Y;
						for ( $i = 0; $i < 3; $i++ )
							$distance[$i] = abs($x - $storeLoc[$i][0] + $y - $storeLoc[$i][1]);
						for ( $i = 0; $i < 3; $i++ )
							if ( $distance[$i] == min($distance) )
								break;
						//$contentStr = $postObj->Location_X . " " . $postObj->Location_Y . "\n" . $distance[0] . " " . $distance[1] . " " . $distance[2] . " ";
						$contentStr = $this->getStoreInfo($i);
						$contentStr .= "\nCurrent Time : " . date("h:i:sa",time()) . "\n\nMap: \n";
						$contentStr .= "http://maps.googleapis.com/maps/api/staticmap?zoom=16&size=640x640&scale=2&maptype=roadmap&markers=color:red%7Clabel:A%7C" . $storeLoc[$i][0] . "," . $storeLoc[$i][1] . "&format=jpg&sensor=false";
						$result = response_text($postObj, $contentStr);
						break;
				}
            	echo $result;
        }else {
        	echo "";
        	exit;
        }
    }
	
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	private function handleEvent($postObj)
	{
		switch ($postObj->Event)
		{
			case "subscribe":
				$record=array(
				'title' => 'Welcome to Bakerzin!',
				'description' => 'Type "bakerzin" to display all functions. And then enter the function code to query. Or...' . "\n\n" . 'Type "info" to display the opening hours and location.' . "\n\n" . 'Type "menu" to display a list of menu items.' . "\n\n" . 'Type "Q&A" to display terms and conditions.' . "\n\n" . 'Type "where" to dispaly a map of all locations of Bakerzin in Singapore.' . "\n\n" . 'Send LOCATION to show nearest outlet.',
				'picUrl' => __ROOT__ . '/main/bakerzin.jpg',
				'url' => ''
				);
				$resultStr = response_news($postObj, $record);
				break;
			default :
				$contentStr = "Unknow Event: ".$object->Event;
				$resultStr = response_text($postObj, $contentStr);
				break;
		}
		return $resultStr;
	}
	
	private function handleText($postObj)
	{
		$keyword = trim($postObj->Content); 
		if(!empty( $keyword ))
		{
			
			switch( $keyword )
			{
				case "a" :
				case "A" :
				case "info" :
				case "Info" :
					$contentStr = "Current Time : " . date("h:i:sa",time()) . "\n\n";
					for ($i = 0; $i < 3; $i++)
						$contentStr .= $this->getStoreInfo($i) . "\n";
					$resultStr = response_text($postObj, $contentStr);
					break;
				case "b" :
				case "B" :
				case "menu" :
				case "Menu" :
					$record[0]=array(
					'title' => 'A Selectioon of Gourmet Delicates',
					'description' => 'null',
					'picUrl' => __ROOT__ . '/menu/menu-title.jpg',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'B1. DESSERTS',
					'description' =>'',
					'picUrl' => __ROOT__ . '/menu/desserts.bmp',
					'url' =>''
					);
					$record[2]=array(
					'title' => 'B2. APPETISERS & STARTERS',
					'description' =>'',
					'picUrl' => __ROOT__ . '/menu/A&S.bmp',
					'url' => ''
					);
					$record[3]=array(
					'title' => 'B3. PASTA',
					'description' =>'',
					'picUrl' => __ROOT__ . '/menu/pasta.bmp',
					'url' => ''
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
				case "b1" :
				case "B1" :
					$record[0]=array(
					'title' => 'DESSERTS',
					'description' => 'null',
					'picUrl' => __ROOT__ . '/desserts/desserts.bmp',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'TIRAMISU',
					'description' =>'',
					'picUrl' => __ROOT__ . '/desserts/tiramisu.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296610&idx=2&sn=f8e95cae08a827951dbe59d2b6b13f23#rd'
					);
					$record[2]=array(
					'title' => 'ADAGIO',
					'description' =>'',
					'picUrl' => __ROOT__ . '/desserts/adagio.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296610&idx=3&sn=47fffcec06bffb15b90192ec958e88c6#rd'
					);
					$record[3]=array(
					'title' => 'OPERA',
					'description' =>'',
					'picUrl' => __ROOT__ . '/desserts/opera.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296610&idx=4&sn=92c6aef704facf60d6940740283f946d#rd'
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
				case "b2" :
				case "B2" :
					$record[0]=array(
					'title' => 'APPETISERS & STARTERS',
					'description' => 'null',
					'picUrl' => __ROOT__ . '/A&S/A&S.bmp',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'BAKED CHICKEN WINGS',
					'description' =>'',
					'picUrl' => __ROOT__ . '/A&S/BCW.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296670&idx=2&sn=e64df5c94cd5722b5919578350f8e07e#rd'
					);
					$record[2]=array(
					'title' => 'BAKED PORK SAUSAGES',
					'description' =>'',
					'picUrl' => __ROOT__ . '/A&S/BPS.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296670&idx=3&sn=c511224e45a9932ceb6c8feffdb954af#rd'
					);
					$record[3]=array(
					'title' => 'BAKED POTATO WEDGES',
					'description' =>'',
					'picUrl' => __ROOT__ . '/A&S/BPW.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296670&idx=4&sn=e13f8ae1d644db60d077954adbb58d30#rd'
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
				case "b3" :
				case "B3" :
					$record[0]=array(
					'title' => 'PASTA',
					'description' => 'null',
					'picUrl' => __ROOT__ . '/A&S/A&S.bmp',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'CARBONARA',
					'description' =>'',
					'picUrl' => __ROOT__ . '/pasta/carbonara.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296738&idx=2&sn=c0c646d2a2a2e939f9c250c8b3885784#rd'
					);
					$record[2]=array(
					'title' => 'CREAM MUSHROOM PENE',
					'description' =>'',
					'picUrl' => __ROOT__ . '/pasta/CMP.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296738&idx=3&sn=e78e0ae99dc91c00df4af98f598df251#rd'
					);
					$record[3]=array(
					'title' => 'SEAFOOD PASTA',
					'description' =>'',
					'picUrl' => __ROOT__ . '/pasta/SP.bmp',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201296738&idx=4&sn=6d990c9460d05cc1697e448e46ea6fc8#rd'
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
				case "c" :
				case "C" :
				case "q&a" :
				case "Q&A" :
				case "Q&a" :
					$record[0]=array(
					'title' => 'Terms & Conditions',
					'description' =>'',
					'picUrl' => __ROOT__ . '/T&C-title.jpg',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'C1. DELIVERY CHARGES',
					'description' => 'DELIVERY CHARGES',
					'picUrl' => '',
					'url' =>''
					);
					$record[2]=array(
					'title' => 'C2. AMENDMENTS AND CANCELLATION',
					'description' => 'AMENDMENTS AND CANCELLATION',
					'picUrl' => '',
					'url' => ''
					);
					$record[3]=array(
					'title' => 'C3. DELIVERY INFORMATION',
					'description' => 'DELIVERY INFORMATION',
					'picUrl' => '',
					'url' => ''
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
				case "c1" :
				case "C1" :
					$contentStr = "1. A delivery surcharge of S$20 + GST per location applies for all item(s) purchase.\n\n2. Specific Delivery Time is subject to an additional charge of S$30 + GST. (+/- 30 minutes from required timing acceptable for this surcharge)";
					$resultStr = response_text($postObj, $contentStr);
					break;
				case "c2" :
				case "C2" :
					$contentStr = "1. Refund only applies to cancellation made with written e-mail with more than 2 working days notice from the date of delivery/collection. And there is an administrative charge of S$10 + GST.\n\n2. Any amendments to orders require at least 2 working day notice from the date of delivery/collection (subject to availability) and there is an administrative charge of S$10 + GST.";
					$resultStr = response_text($postObj, $contentStr);
					break;
				case "c3" :
				case "C3" :
					$contentStr = "1. Delivery not applicable to Jurong Island, restricted airline and cargo area as well as other locations not in mainland Singapore.\n\n2. While Bakerzin endeavors to fulfill all deliveries, Bakerzin Holdings Pte Ltd shall not be liable or responsible for any late delivery or failure to deliver the products ordered due to unforeseen circumstances beyond control.\n\n3. Bakerzin Holdings Pte Ltd is not responsible for undelivered products due to the absence of the recipient or wrong address given.";
					$resultStr = response_text($postObj, $contentStr);
					break;
				case "where" :
				case "Where" :
					$record=array(
					'title' => 'Stroe Locator',
					'description' => 'A : Jurong Point' . "\n" . 'B : Northpoint' . "\n" . 'C : Gardens by the Bay',
					'picUrl' => __ROOT__ . '/staticmap.jpg',
					'url' => 'http://mp.weixin.qq.com/s?__biz=MzAwMzAyOTM0Mg==&mid=201297247&idx=1&sn=0e7a2fa4901c211206b5bceb3e273d1f#rd'
					);
					$resultStr = response_news($postObj, $record);
					break;
				case "testing" :
				case "Testing" : 
					$record=array(
					'title' => 'Testing',
					'description' => '' . __ROOT__ . '/main/bakerzin.jpg',
					'picUrl' => '',
					'url' => ''
					);
					$resultStr = response_news($postObj, $record);
					break;
				case "bakerzin" :
				case "Bakerzin" :
				default:
					$record[0]=array(
					'title' => 'Welcome to Bakerzin!',
					'description' => 'null',
					'picUrl' => __ROOT__ . '/main/bakerzin.jpg',
					'url' => ''
					);
					$record[1]=array(
					'title' => 'A. Basic Info',
					'description' => 'Opening Hour, Location',
					'picUrl' => __ROOT__ . '/main/about-us.jpg',
					'url' => ''
					);
					$record[2]=array(
					'title' => 'B. Menu',
					'description' => 'A Selectioon of Gourmet Delicates',
					'picUrl' => __ROOT__ . '/main/menu.jpg',
					'url' => ''
					);
					$record[3]=array(
					'title' => 'C. Terms & Conditions',
					'description' => 'DELIVERY CHARGES, AMENDMENTS AND CANCELLATION, DELIVERY INFORMATION',
					'picUrl' => __ROOT__ . '/main/T&C.jpg',
					'url' => ''
					);
					$resultStr = response_Multiplenews($postObj, $record);
					break;
			}
		}else{
			echo "Input something...";
		}
		return $resultStr;
	}
	
	private function getStoreInfo($index)
	{
		switch ( $index )
		{
			case 0 : 
					$contentStr = "Jurong Point\nOpen Daily: 11:30am - 11:00pm\n63 Jurong West Central 2 #03-58/59";
					$leftminutes = floor(mktime(23, 0, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
					if ( $leftminutes > 0 && $leftminutes < 690)
						$contentStr .= "\nLeft " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes before closing.\n";
					else {
						$leftminutes = floor(mktime(11, 30, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
						if ( $leftminutes  < 0 )
							$leftminutes = -$leftminutes;
						$contentStr .= "\nWe are now closed. Open again in " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes.\n";
					}
					break;
			case 1 :
					$contentStr = "Northpoint\nOpen Daily: 11:30am - 11:00pm\n930 Yishun Ave 2 #01-44";
					$leftminutes = floor(mktime(23, 0, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
					if ( $leftminutes > 0 && $leftminutes < 690)
						$contentStr .= "\nLeft " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes before closing.\n";
					else {
						$leftminutes = floor(mktime(11, 30, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
						if ( $leftminutes  < 0 )
							$leftminutes = -$leftminutes;
						$contentStr .= "\nWe are now closed. Open again in " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes.\n";
					}
					break;
			case 2 :
					$contentStr = "Gardens by the Bay\nOpen Daily: 10:30am - 9:00pm\n18 Marina Garden Drive #03-03";
					$leftminutes = floor(mktime(21, 0, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
					if ( $leftminutes > 0 && $leftminutes < 630)
						$contentStr .= "\nLeft " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes before closing.\n";
					else {
						$leftminutes = floor(mktime(10, 30, 0, 0, 0, 0) - mktime(date("H"), date("i"), date("s"), 0, 0, 0)) / 60;
						if ( $leftminutes  < 0 )
							$leftminutes = -$leftminutes;
						$contentStr .= "\nWe are now closed. Open again in " . floor($leftminutes / 60) . " hours " . $leftminutes % 60 . " minutes.\n";
					}
					break;
		}
		return $contentStr;
	}
}

?>