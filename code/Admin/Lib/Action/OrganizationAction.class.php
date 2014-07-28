<?php

class OrganizationAction extends CommonAction {
    public function index() {
        $og = M("Organization");
       $where = array
       (
       		venno=>$_SESSION['login_venno'],
       );
       
       $catList = $og->field('id,pid,title')->where($where)->order('pid,sort')->select();

       
        $list = outTreeList(arrToTree($catList, 0), 0);

        $this->assign('list', parent::GetCurPage($list));
        $this->display();
    }
    
    public  function insert()
    {
    	$name=$this->getActionName();
    	$model = CM($name);
    	 
    	if (false === $model->create ())
    	{
    		$this->error ( $model->getError () );
    	}
    	 
    	$arr=$model->create ();
    
    	$arr["venno"]=$_SESSION['login_venno'];
    
    	//保存当前数据对象
    	$list=$model->add ($arr);
    	 
    	if ($list!==false) { //保存成功
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		$this->success ('新增成功!');
    	}
    	else
    	{
    		//失败提示
    		$this->error ('新增失败!');
    	}
    	parent::insert();
    }
    

    public function _before_add() {
        $pid = 0;
        if ($_GET['pid'] != '') {
            $pid = $_GET['pid'];
        }
        $this->assign('pid', $pid);
    }

    public function foreverdelete() {
        $model = M("Organization");
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

?>