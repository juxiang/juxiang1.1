<?php
// 节点模型
class VendorModel extends CommonModel {
		public $_validate	=	array(
		array('venname','require','客户单位不能为空'),
		array('venname','','客户单位已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto		=	array(
		array('cdate','time',self::MODEL_INSERT,'function'),
		array('mdate','time',self::MODEL_UPDATE,'function'),
		);}
?>