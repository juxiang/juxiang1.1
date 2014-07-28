<?php
// 后台用户模块
class VenuserAction extends CommonAction {
	
	public function  index(){
		
		$con = parent::_search('Venuser');
		
		//dump($con);
		//die;
		
		$sql="select t1.id,t2.account,t2.nickname,t3.venno,t3.venname  from wx_venuser as t1" 
					." inner join `wx_user` as t2 on t1.account=t2.account"
					." inner join `wx_vendor` as t3 on t1.venno=t3.venno";
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
    public function treeLookup() {
        $gd = M("Organization");
        
        $where = array
        (
        		venno=>$_SESSION['login_venno'],
        );
        
        $menu = $gd->field('id,pid,title')->where($where)->select();
        $menu = arrToTree($menu, 0);
      
        $this->assign('menu', $menu);
        $this->display();
    }
    
	function _filter(&$map){
		  $map['id'] = array('egt',2);
		$map['account'] = array('like',"%".$_POST['account']."%");
	}
	    
	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

    public function treeLookupven() {
    	$gd = M("Vendor");
    
    	$where = array
    	(
    			venno=>$_SESSION['login_venno'],
    	);
    
    	$menu = $gd->field('id,pid,title')->where($where)->select();
    	$menu = arrToTree($menu, 0);
    
    	$this->assign('menu', $menu);
    	$this->display();
    }
    
}
?>