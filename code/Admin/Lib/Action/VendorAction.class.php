<?php

class VendorAction extends CommonAction {
    
	
    public function index ()
    {
        $con = parent::_search('Vendor');        
     	
     	$list = M("Vendor")->where($con)->order("venno")->select();
        
        $this->assign('list', parent::GetCurPage($list));
        $this->display();
    } 
    
    //检查帐号
    public function checkVenname() {
    	
    	$User = M("Vendor");
    	// 检测用户名是否冲突
    	$name  =  $_REQUEST['venname'];
    	$result  =  $User->getByAccount($name);
    	if($result) {
    		$this->error('该客户已经存在！');
    	}else {
    		$this->success('该客户可以使用！');
    	}
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
		
		$getno= CM("Getno");
			
		$venno = $getno->_By_Type("10","1000001");

		//dump($venno);
		//die;
		
		$arr["venno"]=$venno;
		$arr["cperson"]=$_SESSION['loginAccount'];
		$arr["status"]=1;
		
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
    
    public function edit (){
        $og=M("Vendor")->order("id")->select();
        $array=array();
        foreach($og as $val){
            $array[$val['id']]=$val['title'];
        }
        
        
        $this->gdCat=$array;
        parent::edit();
    } 
    
    public function update()
    {
    	$name=$this->getActionName();
    	
    	$model = CM($name);
    	
    	if (false === $model->create ())
    	{
    		$this->error ( $model->getError () );
    	}
    	 
    	$arr=$model->create ();
    	
    	$arr["mperson"]=$_SESSION['loginAccount'];
    	$arr["status"]=1;
    	
    	//保存当前数据对象
    	$list=$model->save ($arr);
    	 
    	if ($list!==false) { //保存成功
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		$this->success ('编辑成功!');
    	}
    	else
    	{
    		//失败提示
    		$this->error ('编辑失败!');
    	}
		parent::update();	
    }
    
    public function editwx (){
    	$og=M("Vendor")->order("id")->select();
    	$array=array();
    	foreach($og as $val){
    		$array[$val['id']]=$val['title'];
    	}
    	
    	$this->gdCat=$array;
    	parent::edit();
    }
    
    public function updatewx()
    {
    	
    	$name=$this->getActionName();
    
    	$model = CM($name);
    	
    	
    	if (false === $model->create ())
    	{
    		$this->error ( $model->getError () );
    	}
		
    	$arr=$model->create ();
    
    	foreach($_FILES as $k=>$v)
    	{
    		
    		$up[]=uploadfile_company($arr["venno"],$k,$v);//调用公司相关图片上传
    		
    	}
    	
    	$arr["logo_file"]=$up[0]["savepath"].$up[0]["savename"];
    	$arr["business_file"]=$up[1]["savepath"].$up[1]["savename"];
    	$arr["organiz_file"]=$up[2]["savepath"].$up[2]["savename"];
    	$arr["identity_file"]=$up[3]["savepath"].$up[3]["savename"];
    	//保存当前数据对象
    	$list=$model->save ($arr);
    
    	if ($list!==false) { //保存成功
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		$this->success ('编辑成功!');
    	}
    	else
    	{
    		//失败提示
    		$this->error ('编辑失败!');
    	}
    	parent::update();
    }
    
    public function editwxce (){
    	$og=M("Vendor")->order("id")->select();
    	$array=array();
    	foreach($og as $val){
    		$array[$val['id']]=$val['title'];
    	}
    	 
    	$this->gdCat=$array;
    	parent::edit();
    }
    
    public function updatewxce()
    {
    	 
    	$name=$this->getActionName();
    
    	$model = CM($name);
    	 
    	if (false === $model->create ())
    	{
    		$this->error ( $model->getError () );
    	}
    
    	$arr=$model->create ();
    
    	foreach($_FILES as $k=>$v)
    	{
        	$up[]=uploadfile_company($arr["venno"],$k,$v);//调用公司相关图片上传    
    	}
    	
    	if(empty($up[0]["savepath"]))
    		$arr["wxbook_file"]=$up[0]["savepath"].$up[0]["savename"];
    	if(empty($up[1]["savepath"]))
    		$arr["wxdcode_file"]=$up[1]["savepath"].$up[1]["savename"];
    	
    	//保存当前数据对象
    	$list=$model->save ($arr);
    
    	if ($list!==false) { //保存成功
    		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
    		$this->success ('编辑成功!');
    	}
    	else
    	{
    		//失败提示
    		$this->error ('编辑失败!');
    	}
    	parent::update();
    }
    
    public  function downloadImg()
    {
    	$og=M("Vendor")->where('venno='.$_GET['venno'])->select();
		
    	$file=$og[0][$_GET['file']]; 
    	$name=rand(1, 10000).".jpg";
    	downloadFile($file,$name);
    	
    }
}
?>