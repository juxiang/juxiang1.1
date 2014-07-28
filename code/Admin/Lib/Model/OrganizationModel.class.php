<?php
// 节点模型
class OrganizationModel extends CommonModel {
	protected $_validate	=	array(
		//array('inner_seq','checkRepeat','内部序列重复',0,'callback'),
            //array('sort','','内部序列重复！',0,'unique'), // 在新增的时候验证name字段是否唯一

		);

	public function checkRepeat() {
		$map['cat_id']	 =	 $_POST['cat.id'];
                $map['inner_seq']	 =	 $_POST['inner_seq'];
		$result	=	$this->where($map)->field('id')->find();
                //echo($result);
        if($result) {
        	return false;
        }else{
			return true;
		}
	}
}
?>