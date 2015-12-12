<?php	
	function response_text($object, $content){
		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
		$resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
		return $resultStr;
	}
					
	function response_news($object, $newsContent){
		$newsTplHead = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>1</ArticleCount>
						<Articles>";
		$newsTplBody = "<item>
						<Title><![CDATA[%s]]></Title> 
						<Description><![CDATA[%s]]></Description>
						<PicUrl><![CDATA[%s]]></PicUrl>
						<Url><![CDATA[%s]]></Url>
						</item>";
		$newsTplFoot = "</Articles>
						<FuncFlag><![CDATA[%s]]></FuncFlag>
						</xml>";
		$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time());
		$title = $newsContent['title'];
		$desc = $newsContent['description'];
		$picUrl = $newsContent['picUrl'];
		$url = $newsContent['url'];
		$body = sprintf($newsTplBody, $title, $desc, $picUrl, $url);
		$FuncFlag = 0;
		$footer = sprintf($newsTplFoot, $FuncFlag);
		return $header.$body.$footer;
	}
	
	function response_Multiplenews($object, $newsContent){
		$bodyCount = count($newsContent);
		$bodyCount = $bodyCount < 10 ? $bodyCount : 10;
		$newsTplHead = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>". $bodyCount . "</ArticleCount>
						<Articles>";
		$newsTplBody = "<item>
						<Title><![CDATA[%s]]></Title> 
						<Description><![CDATA[%s]]></Description>
						<PicUrl><![CDATA[%s]]></PicUrl>
						<Url><![CDATA[%s]]></Url>
						</item>";
		$newsTplFoot = "</Articles>
						<FuncFlag><![CDATA[%s]]></FuncFlag>
						</xml>";
		$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time(), $bodyCount);
		foreach($newsContent as $key => $value){
			$title = $newsContent['title'];
			$desc = $newsContent['description'];
			$picUrl = $newsContent['picUrl'];
			$url = $newsContent['url'];
			$body .= sprintf($newsTplBody, $value['title'], $value['description'], $value['picUrl'], $value['url']);
		}
		$FuncFlag = 0;
		$footer = sprintf($newsTplFoot, $FuncFlag);
		return $header.$body.$footer;
	}
?>