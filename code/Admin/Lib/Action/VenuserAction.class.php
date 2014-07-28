<?php
// 后台用户模块
class VenuserAction extends CommonAction {
	
	public function  index(){
		
		$con = parent::_search('Venuser');
		
		
		if(!empty($con['account'][1]) )
			$where=" and t1.account like '".$con['account'][1]."'";
		
	
		
		$sql="select t1.id,t2.account,t2.nickname,t3.venno,t3.venname  from wx_venuser as t1" 
					." inner join `wx_user` as t2 on t1.account=t2.account"
					." inner join `wx_vendor` as t3 on t1.venno=t3.venno"
					." where t3.venno=$_SESSION[login_venno]" .$where;
		
		$arr=M("Venuser");
		$list= $arr->query($sql);
		//$list = M("Venuser")->where($con)->order("venno")->select();		
		
		$this->assign('list', parent::GetCurPage($list));
		$this->display();
		
	}
    public function edit (){
        $og=M("Organization")->order("id")->select();
        $array=array();
        foreach($og as $val){
            $array[$val['id']]=$val['title'];
        }
        $this->gdCat=$array;
        parent::edit();
    } 
   
    
	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("Venuser");
		
		
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			
			
			
			// 写入帐号数据
			$v=$_POST['venno'];
			$a=$_POST['cat_account'];
			
			$sql="insert into `wx_venuser`(`venno`,`account`) values('$v','$a')";
				
			if(	 $User->execute($sql)) {
			
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
		parent::insert();
	}
	
	public function treeLookup() {
		
		
		$gd = M("User");
	
		if($_SESSION['loginAccount'] !='admin')
			$where = array
			(
				venno=>$_SESSION['login_venno'],
			);
		else 
			$where = array
			(
					venno=>$_SESSION['login_venno'],
			);
	
		
		$menu = $gd->field('id,account,nickname')->where($where)->select();
		
	
		$this->assign('list', $menu);
		$this->display();
	}

    public function changeaccindex() {
    	
    	$con = parent::_search('Venuser');
    	$gd = M("User");
    	
    	if(!empty($con['account'][1]) )
    		$where=" and account like '".$con['account'][1]."'";
    
    	$sql="select 'id',`account`,`nickname` from `wx_user` where 1=1 ".$where;
    	
    	$menu = $gd->query($sql);
		
		$this->assign('list', $menu);
		$this->display();
    }
    
}
?>