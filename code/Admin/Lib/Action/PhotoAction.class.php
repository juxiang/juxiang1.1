<?php
class PhotoAction extends CommonAction{
	public function index() {
		$con = parent::_searchid('Photo');
		$con['status']=array('gt',0);
		$ph = D("Photo");
		$al = D('Album');
		$lista=$al->field('id,pid,title')->order('pid,sort')->select();
		if(!empty($con)){
			$con['name']=array('like','%'.$con['name'].'%');
			$catList = $ph->relation(true)->where($con)->order('aid,sort')->select();
		}else{
			$catList = $ph->relation(true)->order('aid,sort')->select();
		}
		$list=$catList;
		$listall = outTreeList(arrToTree($lista,0),0);
		$this->assign('list', parent::GetCurPage($list));
		$this->assign('listall',$listall);
		$this->display();
	}	
	public function show() {
		$aid=I('aid');
		$con = parent::_searchid('Photo');
		$ph = D("Photo");
		$al = D('Album');
		$lista=$al->field('id,pid,title')->order('pid,sort')->select();
		if(!empty($con)){
			if(!empty($con['name'])) 
				$con['name']=array('like','%'.$con['name'].'%');
			//p($con);
			$catList = $ph->relation(true)->where($con)->order('aid,sort')->select();
			//$list = outTreeList(arrToTree($catList,0),0);
			$list=$catList;
			p($list);die;
		}else{
			$con=array(
					'pid' =>$aid,
			);
			$catList = $al->where($con)->order('pid,sort')->select();
			$list=$catList;
			
		}
		$listall = outTreeList(arrToTree($lista,0),0);
		$this->assign('list', parent::GetCurPage($list));
		$this->assign('listall',$listall);
		$this->display();
	}
	public function _before_add() {
		$pid = 0;
		if ($_GET['pid'] != '') {
			$pid = $_GET['pid'];
		}
		$al = D('Album');
		$lista=$al->field('id,pid,title')->order('pid,sort')->select();
		$this->listall = outTreeList(arrToTree($lista,0),0);
		$this->assign('pid', $pid);
		$this->assign('time',time());
		//p($_SESSION);die;
	}
	public function adds() {
		$pid = 0;
		if ($_GET['pid'] != '') {
			$pid = $_GET['pid'];
		}
		$al = D('Album');
		$lista=$al->field('id,pid,title')->order('pid,sort')->select();
		$this->listall = outTreeList(arrToTree($lista,0),0);
		$this->assign('pid', $pid);
		$this->display();
	}
	public function _before_edit() {
		$id = null;
		if ($_GET['id'] != '') {
			$id = $_GET['id'];
		}
		$ph = D('Photo');
		$list=$ph->relation(true)->where('id='.$id)->select();
		$this->assign('list', $list);
		$al = D('Album');
		$lista=$al->field('id,pid,title')->order('pid,sort')->select();
		$this->listall = outTreeList(arrToTree($lista,0),0);
	}
	public function picUpload(){
		$ph=D('Photo');
		$data=$ph->create();
		$data['venno']=session('login_venno');
		if($_FILES){
			$up=uploadpic();
			if(!empty($up[0]['hash'])){
				$data['picpath']=$up[0]['savepath'].$up[0]['savename'];
				$data['picpath_s']=$up[0]['savepath'].'s_'.substr($up[0]['savename'], 9);
				$data['picpath_m']=$up[0]['savepath'].'m_'.substr($up[0]['savename'], 9);
				$res=$ph->add($data);
				if($res){
					$return['message']='图片添加成功';
					$return['statusCode']="200";
					$return["callbackType"]="closeCurrent";
				}else{
					$return['message']='图片添加失败';
				}
			}else{
				$return['message']=$up;
			}
		}
		if ($return['statusCode']==200) { //保存成功
			import ( "ORG.Util.Cookie" );
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		//$url=Cookie::get ( '_currentUrl_' );
    		$this->success ('图片添加成功!');
    	}
    	else
    	{
    		//失败提示
    		$this->error("图片添加失败!".$return['message']);
    	}
	}
	public function picUpdate(){
		$id = null;
		$id=$_REQUEST ['id'];
		$ph=D('Photo');
		$data=$ph->create();
		$res=$ph->where('id='.$id)->save($data);
		if ($res) { //保存成功
			import ( "ORG.Util.Cookie" );
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('修改成功!');
		}
		else
		{
			//失败提示
			$this->error("修改失败!");
		}
	}
	public function foreverdelete() {
		$model = M("Photo");
		if (!empty($model)) {
			$id = $_REQUEST ['id'];
			if (isset($id)) {
				$con['id'] = $id;
				if (false !== $model->where($con)->delete()) {
					$this->success('删除成功！');
				} else {
					$this->error('删除失败！');
				}
			
			} else {
				$this->error('非法操作');
			}
		}
	}
	public function listDelete(){
		//彻底删除
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$condition = array ($pk => array ('in', explode(',',$id) ) );
			if (false !== $model->where ( $condition )->setField ( 'status', - 1 )) {
				$this->success ('删除成功！');
			} else {
				$this->error ('删除失败！');
			}
		}
		$this->forward ();
	}
}