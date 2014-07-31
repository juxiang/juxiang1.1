<?php
///////////自定义函数开始
//格式化打印数据
function p($array){
	dump($array,true,'<pre>',0);//$array是欲处理字符串；第二参数：true，false；第三个参数：；第四个参数；
}
//将数组转化为树形数组
function arrToTree($data, $pid, $lv=0) {
    $tree = array();
    foreach ($data as $k => $v) {
        if ($v['pid'] == $pid) {
            $v['pid'] = arrToTree($data, $v['id'], $lv+1);
            $v['lv']=$lv;
            $tree[] = $v;
        }
    }
    return $tree;
}

function outTreeList($tree, $root) {
    $treeList=array();
    foreach ($tree as $t) {
        if (empty($t['pid'])) {
            $treeList[]=$t;
        } else {
            $treeList[]=$t;
            foreach (outTreeList($t['pid'], $root) as $m)
                $treeList[]=$m;
        }
    }
    return $treeList;
}

//无限级树输出
function outMenu($tree, $root) {
    $html = '';
    foreach ($tree as $t) {
        if (empty($t['pid'])) {
            $html .= '<li><a href="javascript:" onclick=$.bringBack({id:"' . $t[id] . '",title:"' . $t[title] . '"})>' . $t['title'] . '</a></li>';
        } else {
            $html .= '<li><a href="javascript:" onclick=$.bringBack({id:"' . $t[id] . '",title:"' . $t[title] . '"})>' . $t['title'] . '</a><ul>';
            $html .=outMenu($t['pid'], $root);
            $html = $html . '</ul></li>';
        }
    }
    return $html;
}

//单位编制无限级树输出
function outMenu2($tree, $root) {
    $html = '';
    foreach ($tree as $t) {
        if (empty($t['pid'])) { //<a href="__GROUP__/{$item['name']}" target="navTab" rel="{$item['name']}">{$item['title']}</a>
            //$hrefStr=
            $html .= '<li><a href="__GROUP__/ShowAll/Index/key/'.$t[id].'" target="navTab" rel="号码查询">'.$t[title].'</a></li>';
        } else {
            $html .= '<li><a href="__GROUP__/ShowAll/Index/key/'.$t[id].'" target="navTab" rel="号码查询">'.$t[title].'</a><ul>';
            $html .=outMenu2($t['pid'], $root);
            $html = $html . '</ul></li>';
        }
    }
    return $html;
}
///////////自定义函数结束

//公共函数
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}


function qtDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}


function qtDatet($time, $format = 'Y-m-d') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}



// 缓存文件
function cmssavecache($name = '', $fields = '') {
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val ) {
		if (empty ( $fields )) {
			$data [$val [$Model->getPk ()]] = $val;
		} else {
			// 获取需要的字段
			if (is_string ( $fields )) {
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1) {
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else {
				foreach ( $fields as $field ) {
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}
function getStatus($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '禁用';
			$showImg = '<IMG SRC="' . __PUBLIC__ . '/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<IMG SRC="' . __PUBLIC__ . '/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<IMG SRC="' . __PUBLIC__ . '/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			break;
		case 1 :
		default :
			$showText = '正常';
			$showImg = '<IMG SRC="' . __PUBLIC__ . '/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';

	}
	return ($imageShow === true) ?  $showImg  : $showText;

}
function getDefaultStyle($style) {
	if (empty ( $style )) {
		return 'blue';
	} else {
		return $style;
	}

}
function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array ();
	if (isset ( $_ip [$ip] )) {
		return $_ip [$ip];
	} else {
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id) {
	if (Session::is_set ( 'nodeNameList' )) {
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}

function get_pawn($pawn) {
	if ($pawn == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}
function get_patent($patent) {
	if ($patent == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}


function getNodeGroupName($id) {
	if (empty ( $id )) {
		return '未分组';
	}
	if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}

function getCardStatus($status) {
	switch ($status) {
		case 0 :
			$show = '未启用';
			break;
		case 1 :
			$show = '已启用';
			break;
		case 2 :
			$show = '使用中';
			break;
		case 3 :
			$show = '已禁用';
			break;
		case 4 :
			$show = '已作废';
			break;
	}
	return $show;

}

// zhanghuihua@msn.com
function showStatus($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a href="__URL__/resume/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">恢复</a>';
			break;
		case 2 :
			$info = '<a href="__URL__/pass/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			$info = '<a href="__URL__/forbid/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		case - 1 :
			$info = '<a href="__URL__/recycle/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1) {
	return rand_string ( $length, $mode );
}


function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select ( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}
function sort_by($array, $keyname = null, $sortby = 'asc') {
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree ) {
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree ) {
		$inarray [] = $array [$key];
	}
	return $inarray;
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}

/* zhanghuihua */
function percent_format($number, $decimals=0) {
	return number_format($number*100, $decimals).'%';
}
/**
 * 动态获取数据库信息
 * @param $tname 表名
 * @param $where 搜索条件
 * @param $order 排序条件 如："id desc";
 * @param $count 取前几条数据 
 */
function findList($tname,$where="", $order, $count){
	$m = M($tname);
	if(!empty($where)){
		$m->where($where);
	}
	if(!empty($order)){
		$m->order($order);
	}
	if($count>0){
		$m->limit($count);
	}
	return $m->select();
}
function findById($name,$id){
	$m = M($name);
	return $m->find($id);
}
function attrById($name, $attr, $id){
	$m = M($name);
	$a = $m->where('id='.$id)->getField($attr);
	return $a;
}


//CommonModel 自动继承
function CM($name){
	static $_model = array();
	if(isset($_model[$name])){
		return $_model[$name];
	}
$class=$name."Model";
import('@.Model.' . $className);
	if(class_exists($class)){
		$return=new $class();
	}else{
		$return=M("CommonModel:".$name);
	}
	$_model[$name]=$return;

return $return;
}


function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if(isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
//上传图片配置
function uploadpic(){
	import('ORG.Net.UploadFile');
	$config=array(
			'maxSize'           =>  2*1024*1024,
			'allowExts'         =>  array('bmp','jpg','jpeg','png','gif'),
			'thumb'             =>  true,
			'thumbMaxWidth'     =>  '320,800',// 缩略图最大宽度
			'thumbMaxHeight'    =>  '225,600',// 缩略图最大高度
			'thumbPrefix'       =>  's_,m_',// 缩略图前缀
			'autoSub'           =>  true,// 启用子目录保存文件
			'subType'           =>  'date',// 子目录创建方式 可以使用hash date custom
			'thumbPath'         =>  './Public/files/images/pics/',// 缩略图保存路径
			'savePath'          =>  './Public/files/images/pics/',// 上传文件保存路径
			'saveRule'          =>  'uniqid',// 上传文件命名规则
			'uploadReplace'     =>  false,// 存在同名是否覆盖
			'thumbRemoveOrigin'  => false,// 是否移除原图
	);
	$upload=new UploadFile($config);
	if(!$upload->upload()) {// 上传错误提示错误信息
		return $upload->getErrorMsg();
	}else{// 上传成功
		return $upload->getUploadFileInfo();
	}

}
//上传产品文件配置
function uploadfile_product(){
	import('ORG.Net.UploadFile');
	$config=array(
			'maxSize'           =>  -1,
			'allowExts'         =>  array('bmp','jpg','jpeg','png','gif'),
			'thumb'             =>  true,
			'thumbMaxWidth'     =>  '300,800',// 缩略图最大宽度
			'thumbMaxHeight'    =>  '225,600',// 缩略图最大高度
			'thumbPrefix'       =>  'p_,c_',// 缩略图前缀
			'autoSub'           =>  true,// 启用子目录保存文件
			'subType'           =>  'date',// 子目录创建方式 可以使用hash date custom
			'thumbPath'         =>  './Public/files/images/product/',// 缩略图保存路径
			'savePath'          =>  './Public/files/images/product/',// 上传文件保存路径
			'saveRule'          =>  'uniqid',// 上传文件命名规则
			'uploadReplace'     =>  false,// 存在同名是否覆盖
			'thumbRemoveOrigin'  => false,// 是否移除原图
	);
	$upload=new UploadFile($config);
	if(!$upload->upload()) {// 上传错误提示错误信息
		return $upload->getErrorMsg();
	}else{// 上传成功
		return $upload->getUploadFileInfo();
	}

}
//上传公司文件配置
function uploadfile_company($venno,$type,$file){
	import('ORG.Net.UploadFile');
	//dump($file);die;
	$savename=companyrename($venno,$type);

	$config=array(
			'maxSize'           =>  -1,
			'allowExts'         =>  array('bmp','jpg','jpeg','png','gif'),
			'thumb'             =>  true,
			'thumbMaxWidth'     =>  '800',// 缩略图最大宽度
			'thumbMaxHeight'    =>  '600',// 缩略图最大高度
			'thumbPrefix'       =>  '',// 缩略图前缀
			'thumbFile'         =>  $savename,// 缩略图文件名
			'autoSub'           =>  true,// 启用子目录保存文件
			'subType'           =>  'date',// 子目录创建方式 可以使用hash date custom
			'thumbPath'         =>  './Public/files/images/company/',// 缩略图保存路径
			'savePath'          =>  './Public/files/images/company/',// 上传文件保存路径
			'saveRule'          =>  $savename,// 上传文件命名规则
			'uploadReplace'     =>  true,// 存在同名是否覆盖
			'thumbRemoveOrigin'  => false,// 是否移除原图
	);
	$upload=new UploadFile($config);
	if(!$info=$upload->uploadOne($file)) {// 上传错误提示错误信息
		return $upload->getErrorMsg();
	}else{// 上传成功
		return $info[0];
	}

}

function companyrename($venno,$type){
	switch($type){
		case "yinye":
			$savename=$venno.'_01';	break;
		case "zuzhi":
			$savename=$venno.'_02'; break;
		case "shenfenzheng":
			$savename=$venno.'_03'; break;		
		case 'weixinlogo':
			$savename=$venno.'_04'; break;
		case 'shouquanshu':
			$savename=$venno.'_05'; break;
		case 'erweima':
			$savename=$venno.'_06'; break;
		default:
			$savename=time();
	}
	return $savename;
}

 function downloadFile($filename,$showname){
	import('ORG.Net.Http');
	
	Http::download($filename, $showname);
}
//获取一定范围内的随机数字 位数不足补零
function rand_number ($min, $max) {
	return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}
//模板中截取字符串长度
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		if(false === $slice) {
			$slice = '';
		}
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice : $slice;
}
//post请求
function PostRequest($url){
	$ch=curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	return curl_exec($ch);
}
