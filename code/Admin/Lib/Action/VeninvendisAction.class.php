<?php
class VeninvendisAction extends CommonAction{
	//首页
	public function index(){
		//$con=parent::_search('Inven');
		$invennos=M('veninvendis')->where('venno='.session('login_venno'))->field('invenno')->select();
		$arr=array();
		foreach ($invennos as $k=>$v){
			$arr[]=$v['invenno'];
		}
		if(!empty($arr)){
			$con=array(
					'invenno'=>array('not in',$arr),
					'status'=>1,
					'venno'=>session('login_venno')
			);
		}else{
			$con=array(
					'status'=>1,
					'venno'=>session('login_venno')
			);
		}
		//p($arr);die;
		$model=D('InvenView');
		$list=$model->where($con)->order('syscno')->select();
		$this->assign('list',parent::GetCurPage($list));
		$this->display();
	}
	//详情列表
	public function displaylist(){
		$con=parent::_search('Inven');
		$con=array(
				'status'=>1,
				'venno'=>session('login_venno')
		);
		$model=D('VeninvenDisView');
		$list=$model->where($con)->order('syscno')->select();
		//p($list);die;
		$this->assign('list',parent::GetCurPage($list));
		$this->display();
	}
}