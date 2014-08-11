<?php
class WxdisViewModel extends ViewModel{
	//wxbuttons微信菜单；productsdisplay展示明细表；inven存货表;invenpic存货图片表；photo图片表；venninvendis存货详情展示表;
	public $viewFields = array(
			'wxbuttons' =>array('id'=>'bid','venno','title','pid','sort','_as'=>'wxb'),
			'productsdisplay'=>array('invenno','_as'=>'pro','_on'=>'pro.bid=wxb.id'),
			'inven'=>array('id'=>'invid','invenname','_on'=>'pro.invenno=inven.invenno'),
			'invenpic'=>array('photoid','_on'=>'inven.invenno=invenpic.invenno'),
			'photo'=>array('picpath_s','_on'=>'invenpic.photoid=photo.id'),
			'veninvendis'=>array('captions','tables','_on'=>'inven.invenno=veninvendis.invenno'),
	);
}