<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

class PublicAction extends Action {

    // 检查用户是否登录

    protected function checkUser() {
        if (!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->assign('jumpUrl', 'Public/login');
            $this->error('没有登录');
        }
    }

    // 顶部页面
    public function top() {
        C('SHOW_RUN_TIME', false);   // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $model = M("Group");
        $list = $model->where('status=1')->getField('id,title');
        $this->assign('nodeGroupList', $list);
        $this->display();
    }

    // 尾部页面
    public function footer() {
        C('SHOW_RUN_TIME', false);   // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }

    // 菜单页面
    public function menu() {
        $this->checkUser();
        if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu = array();
            // if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])) {
            //如果已经缓存，直接读取缓存
            //$menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
            // }else {
            //读取数据库模块列表生成菜单项
            $node = M("Node");
            $id = $node->getField("id");
            $where['level'] = 2;
            $where['status'] = 1;
            $where['pid'] = $id;
            $list = $node->where($where)->field('id,name,group_id,title')->order('sort asc')->select();
            $accessList = $_SESSION['_ACCESS_LIST'];
            //dump($accessList);
            foreach ($list as $key => $module) {
                if (isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                    //设置模块访问权限
                    $module['access'] = 1;

                    $menu[$module['group_id']][$key] = $module;
                }
            }

            //dump($menu);
            //缓存菜单访问
            $_SESSION['menu' . $_SESSION[C('USER_AUTH_KEY')]] = $menu;
            //}
            if (!empty($_GET['tag'])) {
                $this->assign('menuTag', $_GET['tag']);
            }
            //dump($menu);
            $groups = M("Group")->where(array('group_menu' => "{$_GET['menu']}", 'status' => "1"))->order("sort desc,id desc")->select();
            //dump($groups);
            $this->assign("groups", $groups);
            $this->assign('menu', $menu);
        }
        C('SHOW_RUN_TIME', false);   // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $og = M("Organization");
        $treeMenu = $og->field('id,pid,title')->order("pid,sort")->select();
        $catId=M('Organization')->where("id='".$_SESSION['organizationTreeRoot']."'")->field('pid')->find();
        $treeMenu = arrToTree($treeMenu, $catId['pid']?$catId['pid']:0);
        //p($treeMenu);die;
        $this->assign('treeMenu', $treeMenu);
        $this->display();
    }

    public function main() {
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            'ThinkPHP版本' => THINK_VERSION . ' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign('info', $info);
        $this->display();
    }

    // 用户登录页面
    public function login() {
        if (!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->display();
        } else {
            $this->redirect('Index/index');
        }
    }

    public function index() {
        //如果通过认证跳转到首页
    
        redirect(__APP__);
    }

    // 用户登出
    public function logout() {
        if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
            $this->assign("jumpUrl", __URL__ . '/login/');
            $this->success('登出成功！');
        } else {
            $this->error('已经登出！');
        }
    }

    // 登录检测
    public function checkLogin() {
        if (empty($_POST['account'])) {
            $this->error('帐号错误！');
        } elseif (empty($_POST['password'])) {
            $this->error('密码必须！');
        } elseif (empty($_POST['verify'])) {
            $this->error('验证码必须！');
        }
        //生成认证条件
        $map = array();
        // 支持使用绑定帐号登录
        $map['account'] = $_POST['account'];
        $map["status"] = array('gt', 0);
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }
        import('@.ORG.Util.RBAC');
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if (false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        } else {
            if ($authInfo['password'] != md5($_POST['password'])) {
                $this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
            $_SESSION['organizationTreeRoot'] = $authInfo['cat_id'];
            $_SESSION['email'] = $authInfo['email'];
            $_SESSION['loginAccount'] = $authInfo['account'];
            $_SESSION['loginUserName'] = $authInfo['nickname'];
            $_SESSION['lastLoginTime'] = $authInfo['last_login_time'];
            $_SESSION['login_count'] = $authInfo['login_count'];
            if ($authInfo['account'] == 'admin') {
                $_SESSION['administrator'] = true;
            }
            //保存登录日志
            $log['vc_operation'] = "用户登录：登录成功！";
            $log['vc_module'] = "系统管理";
            $log['creator_id'] = $authInfo['id'];
            $log['creator_name'] = $authInfo['account'];
            $log['vc_ip'] = get_client_ip();
            $log['createtime'] = time();
            M("Log")->add($log);
            //保存登录信息
            $User = M('User');
            $ip = get_client_ip();
            $time = time();
            $data = array();
            $data['id'] = $authInfo['id'];
            $data['last_login_time'] = $time;
            $data['login_count'] = array('exp', 'login_count+1');
            $data['last_login_ip'] = $ip;
            $User->save($data);
            
            //默认登录客户账套
			if($authInfo['account'] =="admin")
				$where="select t1.* from `wx_vendor` as t1 where t1.status=1 order by t1.venno LIMIT 1";
			else 
				$where="select t1.* from `wx_vendor` as  t1 LEFT JOIN 'wx_venuser' as t2 ON t1.venno=t2.venno where t1.status=1 and t2.account='".$authInfo['account']."' LIMIT 1";			
			$list = M("Vendor")->query($where);
			//p($where);
			//p($list);die;
			if (empty($list) ) {
				$this->error('帐号没有后台登陆权限！');
			} else 
			{
				$_SESSION['login_venno'] = $list[0]["venno"];
				$_SESSION['login_venname'] = $list[0]['venname'];
				
			}
			
			
            // 缓存访问权限
            RBAC::saveAccessList();
            $this->success('登录成功！');
        }
    }

    // 更换密码
    public function changePwd() {
        $this->checkUser();
        
        
        //对表单提交处理进行处理或者增加非表单数据
        if (md5($_POST['verify']) != $_SESSION['verify']) {
            $this->error('验证码错误！');
        }
        $map = array();
        $map['password'] = pwdHash($_POST['oldpassword']);
        if (isset($_POST['account'])) {
            $map['account'] = $_POST['account'];
        } elseif (isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id'] = $_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User = M("User");
        if (!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        } else {
            $User->password = pwdHash($_POST['password']);
            $User->save();
            $this->success('密码修改成功！');
        }
    }
    
    // 更换账套
    public function changeaccount() {
    	
        $account=$_SESSION['loginAccount'];
    	//检查用户
    	if (empty($account)) {
    		$this->error('切换错误！');
    	} else {
    		
    		$_SESSION['login_venno'] = $_GET["venno"];
			$_SESSION['login_venname'] = $_GET["venname"];
    		$this->success('切换成功！');
    	}
    }
    
    // 更换账套 加载
    public function changeacc() {
    	
    	
    	$this->checkUser();
    	    	
        $User = M("User");
        $vo = $User->getById($_SESSION[C('USER_AUTH_KEY')]);
        
        if(empty($vo))
        {
        	$this->error('不符用户名！');
        }
       
     
        if($vo["account"]=='admin')
        	$sql="select t1.* from `wx_vendor` as t1 where t1.status=1 ";
     	else 
     		$sql="select t1.* from `wx_vendor` as t1 left join 'wx_venuser' as t2 on t1.venno=t2.venno "
     				." where t1.status=1 and t2.account='".$vo["account"]."' order by t1.venno ";
        
     	$ven=M("vendor");
        $arr=$ven->query($sql);
        
        $this->assign('list', $arr);
        
        $this->display();
    }

    
    
    public function changeaccindex()
    {
    	$this->checkUser();
    	
    	$User = M("User");
    	$vo = $User->getById($_SESSION[C('USER_AUTH_KEY')]);
    	
    	if(empty($vo))
    	{
    		$this->error('不符用户名！');
    	}
    	     
    	//$temp1="";
    	if($_POST["venno"] !=null)
    		$temp1=" and t1.venno=".$_POST['venno'];
    	
    	
    	
    	if($_POST['venname'] !=null)
    		$temp1=$temp1." and t1.venname like '%".$_POST['venname']."%'";
    	
    	if($vo["account"]=='admin')
    		
    		$sql="select t1.* from `wx_vendor` as t1 where t1.status=1 ".$temp1;
    	else
    		$sql="select t1.* from `wx_vendor` as t1 left join 'wx_venuser' as t2 on t1.venno=t2.venno "
    				." where t1.status=1 and t2.account='".$vo["account"]."'".temp1." order by t1.venno ";
    	
    	$ven=M("vendor");
    	$arr=$ven->query($sql);
    	
    	//dump($arr);
    	//die;
    	
    	$this->assign('list', $arr);
    	
    	$this->display();
    }
    
    
    public function profile() {
        $this->checkUser();
        $User = M("User");
        $vo = $User->getById($_SESSION[C('USER_AUTH_KEY')]);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function verify() {
        $type = isset($_GET['type']) ? $_GET['type'] : 'gif';
        import("@.ORG.Util.Image");
        Image::buildImageVerify(4, 1, $type);
    }

// 修改资料
    public function change() {
        $this->checkUser();
        $User = D("User");
        if (!$User->create()) {
            $this->error($User->getError());
        }
        $result = $User->save();
        if (false !== $result) {
            $this->success('资料修改成功！');
        } else {
            $this->error('资料修改失败!');
        }
    }

}

?>