<?php
class InvenDisViewModel extends ViewModel{
	public $viewFields=array(
			'inven'=>array('id','invenno','invenname','status'),
			'sysinvclass'=>array('syscno','syscname','sort','_on'=>'inven.syscno=sysinvclass.syscno'),
			'invenpic'=>array('photoid','_on'=>'inven.invenno=invenpic.invenno'),
			'photo'=>array('name'=>'pname','picpath_s','picpath_m','_on'=>'invenpic.photoid=photo.id'),
			'productsdisplay'=>array('bid','id'=>'proid','_on'=>'productsdisplay.invenno=inven.invenno'),
	);
}