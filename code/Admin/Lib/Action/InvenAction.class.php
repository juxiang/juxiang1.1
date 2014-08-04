<?php
class InvenAction extends CommonAction{
	
	public function index(){
		$con=parent::_search('Inven');
		$con=array('status'=>1,'venno'=>session('login_venno'));
		$model=D('InvenView');
		$list=$model->where($con)->order('syscno,invenno')->select();
		$this->assign('list',parent::GetCurPage($list));
		$this->display();
	}
	//添加之前，将系统分类读取出来
	public function _before_add(){
		$cat = M("sysinvclass");
		$clist=$cat->order('pid,sort')->select();
		$list = outTreeList(arrToTree($clist,0),0);
		$this->assign('list',$list);
	}
	public function insert(){
		$model=M('inven');
		$data=$model->create();
		$data['venno']=session('login_venno');
		$data['invenno']=CM('Getno')->_By_Type('11','1100001');
		$data['photoid']=I('photo_id');
		$rs=$model->add($data);
		if($rs) {
			M('invenpic')->add($data);//添加图片
			$this->success('添加成功！');
		}
		else $this->error("添加失败！");
	}
	public function edit(){
		$cat = M("sysinvclass");
		$clist=$cat->order('pid,sort')->select();
		$list = outTreeList(arrToTree($clist,0),0);
		$model=D('InvenView');
		$con=array(
				'id'=>I('id')
		);
		$data=$model->where($con)->find();
		//p($data);die;
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->display();
	}
	public function update(){
		$model=M('inven');
		$data=$model->create();
		$data['photoid']=I('photo_id');
		//p($data);die;
		$rs=$model->save($data);
		$rs2=M('invenpic')->where('invenno='.$data['invenno'])->save($data);//添加图片
		if($rs||$rs2) {
			$this->success('编辑成功！');
		}
		else $this->error("编辑失败！");
	}
	//图片查找
	public function treeLookup(){
		$ph = D("Photo");
		$menu = $ph->relation(true)->where('status=1')->select();
		$menu = outTreeList(arrToTree($menu,0),0);
		$this->assign('menu', $menu);
		$this->display();
	}
	public function fdelete() {
		$model = M("inven");
		if (!empty($model)) {
			$id = $_REQUEST ['id'];
			if (isset($id)) {
				$con2['id'] = $id;
				if (false !== $model->where($con2)->setField('status', -1)) {
					$this->success('删除成功！');
				} else {
					$this->error('删除失败！');
				}
			} else {
				$this->error('非法操作');
			}
		}
	}
}