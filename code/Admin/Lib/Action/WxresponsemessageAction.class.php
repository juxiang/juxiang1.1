<?php
import('@.ORG.Wechat');
class WxresponsemessageAction extends CommonAction
{
   public function index(){
   		$con = parent::_searchid('wxresponsemessage');
	   	$model=D('wxresponsemessage');
	   	$list=$model->where($con)->select();
	   	$this->assign('list',parent::GetCurPage($list));
	   	$this->display();
   }
   //关键字自动回复设置
	public function Response(){
		if(I('type')=='keyword'){
			$this->content="关键字自动回复设置！";
		}elseif(I('type')=='subscribe'){
			$this->content="首次关注回复设置的回复只能设置一次，如果之前设置过，请删除后，再重新设置！";
		}
		$this->keyword=$_GET['type'];
   		$this->display();
   }
   
   //用户首次关注设置
   public function Subscribe(){
   	
   }
   //群发信息设置
   public function Mass(){
   	
   }
   //消息回复
   public function responseMessage(){
   		
   }
    
   //添加文本回复
   public  function addText(){
   		$this->responseType="text";
   		$this->display();
   }
   public function insertText(){
   		$model=D('Wxresponsemessage');
   		$data=$model->create();
   		if (!$model->create()){
   			// 如果创建失败 表示验证没有通过 输出错误提示信息
   			$this->error ('新增失败!'.$model->getError());
   		}else{
   			// 验证通过 可以进行其他数据操作
	   		$data['Content']=msubstr($data['Content'],0,100);
	   		//p(Cookie::get ( '_currentUrl_' ));die;
	   		//保存当前数据对象
	   		$list=$model->add ($data);
	   		if ($list!==false) { //保存成功
	   			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' )."/Response");
	   			$this->success ('新增成功!');
	   		} else {
	   			//失败提示
	   			$this->error ('新增失败!');
	   		}
   		}
   }
   
   public function insertnews(){
   		$model=D('Wxresponsemessage');
   		$post=I('');
   		//p($post);
   		$data=$model->create();
   		if (!$model->create()){
   			// 如果创建失败 表示验证没有通过 输出错误提示信息
   			$this->error ('新增失败!'.$model->getError());
   		}else{
   			for($i=0;$i<$data['ArticleCount'];$i++){
   				$arr['picid'][$i]=$post['photo'.$i.'_id'];
   				$arr['PicUrl'][$i]=$post['photo'.$i.'_picpath_s'];
   			}
   			$save['picid']=implode(',', $arr['picid']);
   			$save['PicUrl']=implode(',', $arr['PicUrl']);
   			$save['Title']=implode(',', $data['Title']);
   			$save['Description']=implode(',', $data['Description']);
   			$save['Url']=implode(',', $data['Url']);
    		$save['ArticleCount']=$data['ArticleCount'];
   			 $save['venno']=$data['venno'];
   			 $save['keyword']=$data['keyword'];
   			 $save['responseType']='news';
   			$list=$model->add ($save);
	   		if ($list!==false) { //保存成功
	   			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ));
	   			$this->success ('新增成功!');
	   		} else {
	   			//失败提示
	   			$this->error ('新增失败!');
	   		}
   		}
   }
   
   public function editText(){
   		$id=$_GET['id'];
   		$model=D('Wxresponsemessage');
   		$this->data=$model->where('id='.$id)->find();
   		//p($data);die;
   		$this->display();
   }
   public function updateText(){
   	$model=D('Wxresponsemessage');
   	$data=$model->create ();
   	// 更新数据
   	$list=$model->save ($data);
   	if (false !== $list) {
   		//成功提示
   		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
   		$this->success ('编辑成功!');
   	} else {
   		//错误提示
   		$this->error ('编辑失败!');
   	}
   }
   public function treeLookup() {
	   	$ph = D("Photo");
	   	$menu = $ph->relation(true)->where('status=1')->select();
	   	$menu = outTreeList(arrToTree($menu,0),0);
	   	$this->assign('menu', $menu);
	   	$this->display();
   }
   public function newsselect(){
   		$this->keyword=$_GET['kyw'];
   		$this->display();
   }
   public function addnews(){
   		$this->count=I('c');
   		$this->display();
   }
}

?>
