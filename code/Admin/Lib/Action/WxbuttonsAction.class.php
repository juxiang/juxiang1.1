<?php
class WxbuttonsAction extends CommonAction{
	public function index() {
		$wx = M("Wxbuttons");
		$con=array(
				'status'=>1,
				'venno'=>session('login_venno')
		);
		$catList=$wx->where($con)->order('pid,sort')->select();
		$list = outTreeList(arrToTree($catList,0),0);
		//p($list);die;
		$this->assign('list', parent::GetCurPage($list));
		$this->display();
	}	
	public function _before_add() {
		$pid = 0;
		$this->assign('pid', $pid);
	}
	public function add2() {
		$pid = 0;
		if ($_GET['pid'] != '') {
			$pid = $_GET['pid'];
		}
		$this->assign('pid', $pid);
		$this->display();
	}
	function insert() {
		$name=$this->getActionName();
		$model = CM($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data=$model->create();
		$data['ppid']=I('lv');
		$data['venno']=session('login_venno');
		$oneb=$model->where('pid=0')->count();
		$twob=$model->where(array('pid='.$data['pid'],'ppid=2'))->count();
		if(empty($data['pid'])){
			if($oneb==3){
				$this->error("新增失败！一级菜单最多只能添加3个！");
			}
		}else{
			if($twob==5){
				$this->error("新增失败！子菜单最多只能添加5个！");
			}
		}
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
	//微信菜单发布
	public function publish(){
		$buttons=A('Weixin');
		$return=$buttons->buttonsPublish();
		if($return==1){
			$this->success('自定义菜单发布成功！');
		}else {
			$this->error($return);
		}
	}
	public function foreverdelete() {
		$model = M("Wxbuttons");
		if (!empty($model)) {
			$id = $_REQUEST ['id'];
			if (isset($id)) {
				$con['pid'] = $id;
				if ($model->where($con)->count() == 0) {
					$con2['id'] = $id;
					if (false !== $model->where($con2)->delete()) {
						$this->success('删除成功！');
					} else {
						$this->error('删除失败！');
					}
				} else {
					$this->error('请先删除子类！');
				}
			} else {
				$this->error('非法操作');
			}
		}
	}
}