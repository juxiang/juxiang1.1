<?php
class IndexAction extends CommonAction {
	
	// 框架首页
	public function index() {

		if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			//显示菜单项
			$menu = array ();
			$mmm=array();
			//读取数据库模块列表生成菜单项
			$node = M ( "Node" );
			$id = $node->getField ( "id" );
			$where ['level'] = 2;
			$where ['status'] = 1;
			$where ['pid'] = $id;
			$list = $node->where ( $where )->field ( 'id,name,group_id,title' )->order ( 'sort asc' )->select ();
                        
			$accessList = $_SESSION ['_ACCESS_LIST'];
			foreach ( $list as $key => $module ) {
				if (isset ( $accessList [strtoupper ( APP_NAME )] [strtoupper ( $module ['name'] )] ) || $_SESSION ['administrator']) {
					//设置模块访问权限
					$module ['access'] = 1;
					//lxz 修改 获取当前分类的module
					$menu[$module['group_id']][$key]=$module;
                                        $mmm[$module['group_id']]=$module;
				}
			}

			if (! empty ( $_GET ['tag'] )) {
				$this->assign ( 'menuTag', $_GET ['tag'] );
			}
                        
                        $volist = array();
			$volist_candidate=M("GroupClass")->where(array('status'=>1))->order("sort desc, id desc")->select();
                        foreach ($volist_candidate as $al){
                            if($al['menu']=='ywcx')
                            {
                                array_push ($volist, $al);
                                continue;
                            }
                            $n=M('Node');
                            $con['status'] = 1;
                            $con ['level'] = 2;
                            $con2['status'] = 1;
                            $con2['group_menu'] = $al['menu'];
                            $findId=M('Group')->where($con2)->field('id')->select();
                            $con['group_id']=$findId[0]['id'];
                             
                            if(!$con['group_id'] || !$mmm[$con['group_id']]['access']) 
                                continue;
                            if($n->where($con)->count('id'))
                                array_push ($volist, $al);
                        }
			$this->volist=$volist;

			//luz start
			$groups=M("Group")->where(array('group_menu'=>"{$volist[0]['menu']}",'status'=>"1"))->order("sort desc,id desc")->select();	
			$this->assign("groups",$groups);
			//luz end
			$this->assign ( 'menu', $menu );
		}
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', true );
                $og = M("Organization");
                $treeMenu = $og->field('id,pid,title')->order("pid,sort")->select();
                $catId=M('Organization')->where("id='".$_SESSION['organizationTreeRoot']."'")->field('pid')->find();
                $treeMenu = arrToTree($treeMenu, $catId['pid']?$catId['pid']:0);
                $this->assign('treeMenu', $treeMenu);
		$this->display ();
	}

}
?>