<?php
class InvendisAction extends CommonAction{
	public function index(){
		$con=array('status'=>1,'venno'=>session('login_venno'));
		$buttons=M('wxbuttons')->where($con)->order('pid,sort')->select();
		$list = outTreeList(arrToTree($buttons,0),0);
		$this->assign('list',parent::GetCurPage($list));
		$this->display();
	}
	//list1:已经选择的；list：代表未选择的;
	public function edit(){
		$model1=D('InvenDisView');
		$model=D('InvenView');
		$con1=array(
				'bid'=>array('eq',I('id'))
		);
		$list1=$model1->where($con1)->order('syscno')->select();//已经设置展示
		//获取已经设置展示的存货编号
		$arr=array();
		foreach ($list1 as $k=>$v){
			$arr[]=$v['invenno'];
		}
		$con=array(
				'invenno'=>array('not in',$arr)
		);
		//p($con);die;
		$list=$model->where($con)->order('syscno')->select();//不含已经发布的所有存货
		$this->assign('list',parent::GetCurPage($list));
		$this->assign('list1',$list1);
		$this->display();
	}

	//设置显示的存货
	public function setDisplay(){
		$model=M('productsdisplay');
		$data=$model->create();
		$data['invennos']=explode(',',$data['invenno']);
		$data['bid']=$_GET['bid'];
		$data['venno']=session('login_venno');
		for($i=0;$i<count($data['invennos']);$i++){
			$data['invenno']=$data['invennos'][$i];
			$rs[]=$model->add($data);
		}
		if($rs){
			$this->success ('成功！');
		} else {
			$this->error ('失败！');
		}
	}
	//取消显示
	public function foreverdelete(){
		$model=M('productsdisplay');
		$rs=$model->where('id='.$_GET['id'])->delete();
		if($rs){
			$this->success ('删除成功！');
		} else {
			$this->error ('删除失败！');
		}
	}
}