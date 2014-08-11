<?php
class AttributeManageAction extends CommonAction{
	//操作表：系统存货大类sysinvclass(id,syscno,syscname,pid,status);存货大类属性sysinvclassatt(id,syscno,sysano,sysaname,typename,status);大类属性值sysinvclassval(id,sysano,val);sysano和syscno的生成方法：CM('Getno')->_By_Type('参数1','参数2')【参数1:syscno20,sysano21;参数2:初始值】
	
	
	public function index(){
		$type=I('type');//Cat,Attr,AttrValue
		if($type=='Cat'){
			$con['status']=1;
			$cat = M("sysinvclass");
			$clist=$cat->where($con)->order('pid,sort')->select();
			$list = outTreeList(arrToTree($clist,0),0);
		}
		$this->assign('list', parent::GetCurPage($list));
		$this->display($type);
	}
	public function _before_add() {
		$pid = 0;
		if ($_GET['pid'] != '') {
			$pid = $_GET['pid'];
		}
		$this->assign('pid', $pid);
	}
	//搜索
	public function search(){
		$search=I('');
		$search['type']=$_GET['type'];
		if($search['type']=='Cat'){
			empty($search['syscname'])? ' ':$con['syscname']=array('like','%'.$search['syscname'].'%');
		$con['status']=1;
		$cat = M("sysinvclass");
		$clist=$cat->where($con)->order('pid,sort')->select();
		$list = outTreeList(arrToTree($clist,0),0);
		}
		$this->assign('list', parent::GetCurPage($list));
		$this->display($search['type']);
	}
	public function add(){
		$type=I('type');//Cat,Attr,AttrValue
		if($type=='Attr'){
			$cat = M("sysinvclass");
			$clist=$cat->where($con)->order('pid,sort')->select();
			$list = outTreeList(arrToTree($clist,0),0);
		}
		$this->assign('select',$list);
		$this->display($type.'add');//
	}
	public function edit(){
		$type=I('type');//Cat,Attr,AttrValue
		if($type=='Cat'){
			$con = array(
					'id'=>I('id'),
			);
			$cat = M("sysinvclass");
			$data=$cat->where($con)->order('pid,sort')->find();
		}
		$this->assign('data',$data);
		$this->display($type.'update');//
	}
	public function delete(){
		$type=I('type');//Cat,Attr,AttrValue
		$fun=$type.'Delete';
		$this->$fun();
	}
	//分类首页
	public function Cat(){
		$this->display();
	}
	public function CatAddhandle(){
		$model=M('sysinvclass');
		$data=$model->create();
		$data['syscno']=CM('Getno')->_By_Type('20','2000001');
		
		$rs=$model->add($data);
		if($rs) {
			$this->success('添加成功！');
		}
		else $this->error("添加失败！");
	}

	public function CatUpdatehandle(){
		$model=M('sysinvclass');
		$rs=$model->save($model->create());
		if($rs) {
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success('编辑成功！');
		}
		else $this->error("编辑失败！");
	}
	private function CatDelete(){
		
	}
	//属性名管理
	public function Attr(){
	
	}
	public function AttrAddhandle(){
		//p(I());die;
		$model=M('sysinvclassatt');
		$data=$model->create();
		$data['sysano']=CM('Getno')->_By_Type('21','2100001');
		$rs=$model->add($data);
		if($rs) {
			$this->success('添加成功！');
		}
		else $this->error("添加失败！");
	}

	public function AttrUpdatehandle(){
	
	}
	private function AttrDelete(){
	
	}
	//属性值管理
	public function AttrValue(){
	
	}
	public function AttrValueAddhandle(){
	
	}
	public function AttrValueUpdatehandle(){
	
	}
	private function AttrValueDelete(){
	
	}
	public function fdelete() {
		$model = M("sysinvclass");
		if (!empty($model)) {
			$id = $_REQUEST ['id'];
			if (isset($id)) {
				$con['pid'] = $id;
				if ($model->where($con)->count() == 0) {
					$con2['id'] = $id;
					if (false !== $model->where($con2)->setField('status', -1)) {
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