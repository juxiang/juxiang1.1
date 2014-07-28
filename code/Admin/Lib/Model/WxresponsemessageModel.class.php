<?php
class WxresponsemessageModel extends CommonModel{
	protected $_validate	=	array(
			array('keyword','','事件/关键字 已存在！',0,'unique',1),// 在新增的时候验证name字段是否唯一
					
            //array('sort','','内部序列重复！',0,'unique'), // 在新增的时候验证name字段是否唯一

		);
}
?>