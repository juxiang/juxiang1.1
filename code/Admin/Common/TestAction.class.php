<?php
//上传文件的实现方法，放在common.php 中


//请将以上方法放在common/common.php
//调用方法如下
//测试方法
class TestAction extends Action{
	function index(){
		$this->display();
	}
	
	function upProduct(){
		$up=uploadfile_product();//调用公司产品图片上传
		dump($up);
		/*
		array (size=1)
		  0 => 
			array (size=8)
			  'name' => string 'Desert2.jpg' (length=11)
			  'type' => string 'image/jpeg' (length=10)
			  'size' => int 85812
			  'key' => string 'product' (length=7)
			  'extension' => string 'jpg' (length=3)
			  'savepath' => string './Public/files/images/product/' (length=30)
			  'savename' => string '20140718/53c88fd8de358.jpg' (length=26)
			  'hash' => string 'ce7b7363226e5f7b9f2cfdc31d014b4d' (length=32)
	  */
	}
	function upCompany(){
		foreach($_FILES as $k=>$v){
			$up[]=uploadfile_company($k,$v);//调用公司相关图片上传
		}
		dump($up);
		//打印样式
		/*array (size=3)
  0 => 
    array (size=7)
      'name' => string 'Desert.jpg' (length=10)
      'type' => string 'image/jpeg' (length=10)
      'size' => int 845941
      'extension' => string 'jpg' (length=3)
      'savepath' => string './Public/files/images/company/' (length=30)
      'savename' => string '20140718/100001_01.jpg' (length=22)
      'hash' => string 'ba45c8f60456a672e003a875e469d0eb' (length=32)
  1 => 
    array (size=7)
      'name' => string 'Jellyfish.jpg' (length=13)
      'type' => string 'image/jpeg' (length=10)
      'size' => int 775702
      'extension' => string 'jpg' (length=3)
      'savepath' => string './Public/files/images/company/' (length=30)
      'savename' => string '20140718/100001_02.jpg' (length=22)
      'hash' => string '5a44c7ba5bbe4ec867233d67e4806848' (length=32)
  2 => 
    array (size=7)
      'name' => string 'Tulips.jpg' (length=10)
      'type' => string 'image/jpeg' (length=10)
      'size' => int 620888
      'extension' => string 'jpg' (length=3)
      'savepath' => string './Public/files/images/company/' (length=30)
      'savename' => string '20140718/100001_03.jpg' (length=22)
      'hash' => string 'fafa5efeaf3cbe3b23b2748d13e629a1' (length=32)
	  */
	}
}

?>