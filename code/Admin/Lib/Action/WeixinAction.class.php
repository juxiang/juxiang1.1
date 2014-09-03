<?php
//微信操作：自定义菜单生成；获取用户分组，删除用户分组，获取关注用户信息，删除用户
class WeixinAction extends Action{
	private $appid;
	private $secret;
	private $openid;
	private $errormessage;
	//自定义菜单发布
	public function buttonsPublish(){
		$model=M('wxbuttons');
		$con=array(
				'venno' =>session('login_venno'),
				'ppid'=>array('lt',3),
		);
		$menu=$model->where($con)->order('sort')->select();
		$menu=arrToTree($menu, 0);
		$buttons=$this->setWeixinbuttons($menu);
		$info=$this->createMenu($menu);
		if($info['errcode']==0){
			return 1;
		}else{
			$this->errormessage[]='自定义菜单生成出错。错误详情：'.$info['errcode'].','.$info['errmsg'];
			foreach ($this->errormessage as $k=>$v){
				$arr.=$v."<br/>";
			}
			return $arr;
		}
	}
	//微信url验证
	public function wxurlverify(){
		//P(1);die;
		//$venno=I('venno');
		$venno=$_GET['venno'];
		$_SESSION['wxvenno']=$venno;
		$model=M('vendor');
		$where=array('venno'=>$venno);
		$data=$model->where($where)->find();
		$token=$data['wxtoken'];
		if (IS_GET) {
			$echoStr=$this->valid($token);
			echo $echoStr;
			die;
		} else {
			return $this->responseMsg();
		}
	}
	/**
	 *  初次校验
	 */
	private function valid($token){
		$echoStr = $_GET["echostr"];
		if($this->checkSignature($token)){
			echo $echoStr;
			exit;
		}else{
			echo $token;
		}
	}
	/**
	 *  校验签名
	 */
	private function checkSignature($token){
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
	
		$token = $token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if($tmpStr == $signature){
			return true;
		}else{
			return false;
		}
	}
	//消息回复
	protected function responseMsg(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$RX_TYPE = trim($postObj->MsgType);
				
			switch ($RX_TYPE)
			{
				case "event":
					$result = $this->receiveEvent($postObj);
					break;
				case "text":
					$result = $this->receiveText($postObj);
					break;
				case "image":
					$result = $this->receiveImage($postObj);
					break;
				case "location":
					$result = $this->receiveLocation($postObj);
					break;
				case "voice":
					$result = $this->receiveVoice($postObj);
					break;
				case "video":
					$result = $this->receiveVideo($postObj);
					break;
				case "link":
					$result = $this->receiveLink($postObj);
					break;
				default:
					$result = "unknow msg type: ".$RX_TYPE;
					break;
			}
			echo $result;
		}else {
			echo "";
			exit;
		}
	}
	private function receiveEvent($object)
	{
		$content = "";
		switch ($object->Event)
		{
			case "subscribe":
				$keyword='subscribe';
				$content=$this->returncontent($keyword);
				break;
			case "unsubscribe":
				$content = "取消关注";
				break;
			case "SCAN":
				$content = "扫描场景 ".$object->EventKey;
				break;
			case "CLICK":
				switch ($object->EventKey)
				{
					default:
						$content = "点击菜单:".$object->EventKey;
						break;
				}
				break;
			case "LOCATION":
				$content = "上传位置:纬度 ".$object->Latitude.";经度 ".$object->Longitude;
				break;
			default:
				$content = "receive a new event: ".$object->Event;
				break;
		}
	
		if(is_array($content)){
			if (isset($content[0]['PicUrl'])){
				$result = $this->transmitNews($object, $content);
			}else if (isset($content['MusicUrl'])){
				$result = $this->transmitMusic($object, $content);
			}
		}else{
			$result = $this->transmitText($object, $content);
		}
		return $result;
	}
	private function receiveText($object)
	{
		$keyword = trim($object->Content);
		$content=$this->returncontent($keyword);
		if(empty($content)){
			$content='谢谢您的留言，我们会尽快回复您！';
		}
		if(is_array($content)){
			if (isset($content[0]['PicUrl'])){
				$result = $this->transmitNews($object, $content);
			}elseif(isset($content['MusicUrl'])){
				$result = $this->transmitMusic($object, $content);
			}
		}else{ 
			$result = $this->transmitText($object, $content);
		}
		return $result;
	}
	
	private function receiveImage($object)
	{
		$content = array("MediaId"=>$object->MediaId);
		$result = $this->transmitImage($object, $content);
		return $result;
	}
	
	private function receiveLocation($object)
	{
		$content = "你发送的是位置，纬度为:".$object->Location_X."；经度为:".$object->Location_Y."；缩放级别为:".$object->Scale."；位置为:".$object->Label;
		$result = $this->transmitText($object, $content);
		return $result;
	}
	
	private function receiveVoice($object)
	{
		if (empty($object->Recognition)){
			$content = array("MediaId"=>$object->MediaId);
			$result = $this->transmitVoice($object, $content);
		}else{
			$content = "你刚才说的是:".$object->Recognition;
			$result = $this->transmitText($object, $content);
		}
	
		return $result;
	}
	
	private function receiveVideo($object)
	{
		$content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
		$result = $this->transmitVideo($object, $content);
		return $result;
	}
	
	private function receiveLink($object)
	{
		$content = "你发送的是链接，标题为:".$object->Title."；内容为:".$object->Description."；链接地址为:".$object->Url;
		$result = $this->transmitText($object, $content);
		return $result;
	}
	
	
	private function returncontent($keyword){
		$model=M('wxresponsemessage');
		$where['venno']=$_SESSION['wxvenno'];
		$where['keyword']=$keyword;
		$data=$model->where($where)->find();
		if($data['responseType']=='text')
			$content=$data['Content'];
		elseif($data['responseType']=='news')
		{
			if($data['ArticleCount']==1)
				$content[] = array("Title"=>$data['Title'], "Description"=>$data['Description'], "PicUrl"=>'http://'.$_SERVER['HTTP_HOST'].__ROOT__.substr($data['PicUrl'], 1), "Url" =>$data['Url']);
			else{
				$array=array();
				$array['Title']=explode(',', $data['Title']);
				$array['Description']=explode(',', $data['Descriptione']);
				$array['PicUrl']=explode(',', $data['PicUrl']);
				$array['Url']=explode(',', $data['Url']);
				for($i=0;$i<$data['ArticleCount'];$i++){
					$content[] = array("Title"=>$array['Title'][$i], "Description"=>$array['Description'][$i], "PicUrl"=>'http://'.$_SERVER['HTTP_HOST'].__ROOT__.substr($array['PicUrl'][$i], 1), "Url" =>$array['Url'][$i]);
				}
			}
		}
		return $content;
		//$r=implode(',', $content[0]);
		//return $r;
	}
	private function returntrasmit($object,$content){
		if(is_array($content)){
			if (isset($content[0]['PicUrl'])){
				$result = $this->transmitNews($object, $content);
			}else if (isset($content['MusicUrl'])){
				$result = $this->transmitMusic($object, $content);
			}
		}else{
			$result = $this->transmitText($object, $content);
		}
		return $result;
	}
	//获取微信的APPID,APPSECRET等信息
	private function getWeixin(){
		$model=M('vendor');
		$con=array(
				'venno' =>session('login_venno'),
		);
		$data=$model->where($con)->field('wx_aid,wx_secret,openid')->find();
		$this->appid=$data['wx_aid'];
		$this->secret=$data['wx_secret'];
		$this->openid=$data['openid'];
		return $data;
	}
	/**
	 *  获取access token
	 */
	private function getAccessToken(){
		if((session('access_token_expiretime')-time())>0){
			return session('access_token');
		}else{
			$this->getWeixin();
			$url=
			"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
			$result=$this->https_request($url);
			if(!empty($result['access_token'])){
				$_SESSION['access_token']=$result["access_token"];
				$_SESSION['access_token_expiretime']=time()+7200;
				return $result["access_token"];
			}else{
				$this->errormessage[]='获取accesstoken值出错。错误详情：'.$result['errcode'].','.$result['errmsg'];
			}
		}
	}
	//远程抓取链接返回值，解密json
	public function https_request($url){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$atjson = curl_exec($ch);
		return $result=json_decode($atjson,true);//json解析成数组
	}
	/**
	 *  创建自定义菜单{"errcode":0,"errmsg":"ok"}
	 */
	private function createMenu($wx_buttons){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getAccessToken();
		$menujson=$this->setWeixinbuttons($wx_buttons);
		 $ch = curl_init();
		 curl_setopt($ch,CURLOPT_URL,$url);
		 curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		 curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		 curl_setopt($ch,CURLOPT_POST,1);
		 curl_setopt($ch,CURLOPT_POSTFIELDS,$menujson);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		 $info = curl_exec($ch);
	
		 if (curl_errno($ch)) {
		 	echo 'Errno'.curl_error($ch);
		 }
	
		 curl_close($ch);
		 return json_decode($info,true);
		 
	
	}
	//获取自定义菜单
	private function getMenu(){
		$url ="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$this->getAccessToken();
		$result=$this->https_request($url);
		return $result;
	}
	//删除菜单按钮
	//返回值为：{"errcode":0,"errmsg":"ok"}
	private function deleteMenu(){
		$url ="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->getAccessToken();
		$result=$this->https_request($url);
		return $result;
	}
	//将自定义菜单按钮转换成微信发布菜单格式
	private function setWeixinbuttons($wx_buttons){
		//根据设置生成微信菜单，菜单信息数组$buttons
		foreach($wx_buttons as $k=>$button){
			if($button['level']==0){
				$arr[$k][]="{'name':'".$button['title']."',";
				if($button['model']==2){
					$arr[$k][]="'type':'click',
							'key':'".$button['value']."'},";
				}else{
					$arr[$k][]="'type':'view',
							'url':'".$button['value']."'},";
				}
			}else{
				$ar=array();
				$arr[$k][]="{'name':'".$button['title']."',
						'sub_button':[";
				foreach($button['pid'] as $j=>$v){
					$ar[$j][]="{'name':'".$v['title']."'";
					if($v['model']==2){
						$ar[$j][]="'type':'click',
								'key':'".$v['value']."'}";
					}else{
						$ar[$j][]="'type':'view',
								'url':'".$v['value']."'}";
					}
					$ar[$j]=implode(",",$ar[$j]);
				}
				$arr[$k][]=implode(",",$ar);
				$arr[$k][]="]},";
			}
			$arr[$k]=implode($arr[$k]);
		}
		$arr=implode($arr);
		$arr=str_replace("'",'"',$arr);
		return $menujson="{\"button\":[".$arr."]}";
	}
	
	private function transmitText($object, $content)
	{
		$textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
		$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
		return $result;
	}
	
	private function transmitImage($object, $imageArray)
	{
		$itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";
	
		$item_str = sprintf($itemTpl, $imageArray['MediaId']);
	
		$textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[image]]></MsgType>
		$item_str
		</xml>";
	
		$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
		return $result;
	}
	
	private function transmitVoice($object, $voiceArray)
	{
	$itemTpl = "<Voice>
	<MediaId><![CDATA[%s]]></MediaId>
</Voice>";
	
        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
	
	        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[voice]]></MsgType>
	$item_str
	</xml>";
	
	$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
	return $result;
		}
	
		private function transmitVideo($object, $videoArray)
		{
		$itemTpl = "<Video>
		<MediaId><![CDATA[%s]]></MediaId>
		<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";
	
        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
	
	        $textTpl = "<xml>
	        <ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[video]]></MsgType>
		$item_str
		</xml>";
	
		$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
		return $result;
		}
	private function transmitNews($object, $newsArray)
	{
		if(!is_array($newsArray)){
			return;
		}
		$itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
		$item_str = "";
		foreach ($newsArray as $item){
			$item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
		}
		$newsTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<Content><![CDATA[]]></Content>
		<ArticleCount>%s</ArticleCount>
		<Articles>
		$item_str</Articles>
		</xml>";
	
		$result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
		return $result;
	}
	
	private function transmitMusic($object, $musicArray)
	{
	$itemTpl = "<Music>
	<Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";
	
        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
	
	        		$textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[music]]></MsgType>
	$item_str
	</xml>";
	
	$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
	return $result;
		}
	
}