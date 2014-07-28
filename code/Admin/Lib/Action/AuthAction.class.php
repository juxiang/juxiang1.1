<?php
import('@.ORG.Wechat');
class AuthAction extends Action{
	private $token;
	public function index(){
		$venno=$_GET['venno'];
		$data=M('vendor')->field('wx_aid,wx_secret,wxurl,wxtoken,openid')->where('venno='.$venno)->find();
		$this->token=$data['wxtoken'];
		$wechat=new Wechat($data['wxtoken']);
		if ($_GET['echostr']) {
			return $wechat;
		}else{
			//消息处理函数
			$request=$wechat->request();//获取请求数据
			if($request['MsgType']=='event'){
				return $this->eventHandle($request);//事件信息处理
			}else{
				$this->messageSave($request);//保存请求数据
				return $this->autoResponse($request);//自动回复
			}
		}
	}
	
	//消息处理函数
	public function messageSave($request){
		$res=M('wxmessage')->add($request);
	}
	public function autoRespose($request){
		//文本内容回复
		$resm=M('wxresponsemessage');
		$con=array(
				'keyword'=>$request['Content'],
		);
		switch ($request['MsgType']){
			case 'text':
				$con=array(
					'keyword'=>'%'.$request['Content'].'%',
				);
				$res=$resm->where($con)->find();
				break;
			case 'image':
			case 'voice':
			case 'video':
			case 'location':
			case 'link':
		}
		return $this->responseMessage($res);
		
	}
	public function responseMessage($res){
		$type=$res['responseType'];
		switch ($type){
			case 'text':
				$content=$res['Content'];
				break;
			case 'news':
				$content=$resm->where('id='.$$res['id'])->field('Title,Description,PicUrl,Url')->select();
				break;
			default:
				$content="谢谢您的留言，我们会尽快回复您！";
		}
		$wechat=new Wechat($this->token);
		return $wechat->response($content,$type);
	}
	public function eventHandle($request){
		$event=$request['Event'];
		$resm=M('wxresponsemessage');
		switch($event){
			case 'subscribe':
				$con=array(
						'keyword'=>'subscribe',
				);
				$res=$resm->where($con)->find();
				break;
			case 'SCAN':
			case 'LOCATION':
			case 'CLICK':
			case 'VIEW':
				
		}
		return $this->responseMessage($res);
	}
}