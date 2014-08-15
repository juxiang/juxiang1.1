<?php
class WxdisplayAction extends Action{
	//首页，接收wx_buttons的二级分类ID，获取三级分类id及名称；
	public function index(){
		$model = D("WxdisView");
		$id=I('id');
		$con=array(
				'bid'=>I('bid'),
				'venno'=>I('venno'),
				'pid'=>I('id'),
		);
		$m=M('wxbuttons');
		$list=$model->where($con)->order('sort')->select();
		$cat=M('wxbuttons')->where('pid='.$id)->order('sort')->select();
		//p($cat);
		//p($list);die;
		$this->assign('list',$list);
		$this->assign('category',$cat);
		$this->display();
	}
	public function detail(){
		$model = D("WxdisView");
		$con=array(
				'bid'=>I('bid'),
				'venno'=>I('venno'),
				'pid'=>I('id'),
				'invenno'=>I('invenno'),
		);
		$list=$model->where($con)->find();
		//p($list);die;
		$this->assign('list',$list);
		$this->display();
	}
}