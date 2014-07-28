<?php
//微信操作：自定义菜单生成；获取用户分组，删除用户分组，获取关注用户信息，删除用户
class WeixinAction extends CommonAction{
	private $appid;
	private $secret;
	private $openid;
	//自定义菜单发布
	public function buttonsPublish(){
		$model=M('wxbuttons');
		$con=array(
				'venno' =>session('login_venno'),
		);
		$menu=$model->where($con)->order('sort')->select();
		$menu=arrToTree($menu, 0);
		$buttons=$this->setWeixinbuttons($menu);
		p($buttons);die;
		return $this->createMenu($menu);
	}
	//获取微信的APPID,APPSECRET等信息
	private function getWeixin(){
		$model=M('vendor');
		$con=array(
				'venno' =>session('login_venno'),
		);
		$data=$model->where($con)->field('wx_aid,wx_secret,openid')->find();
		$this->appid=$data['wx_aid'];
		$this->wx_secret=$data['wx_secret'];
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
			session('access_token')=$result["access_token"];
			session('access_token_expiretime')=time()+7200;
			return $result["access_token"];
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
	
		 echo $info;
	
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
}