<?php
class VeninvenDisViewModel extends ViewModel{
	public $viewFields=array(
			'veninvendis'=>array('id','invenno','captions','tables'),
			'inven'=>array('invenname','venno','status','_on'=>'veninvendis.invenno=inven.invenno'),
			'sysinvclass'=>array('syscno','syscname','sort','_on'=>'inven.syscno=sysinvclass.syscno'),
			'invenpic'=>array('photoid','_on'=>'inven.invenno=invenpic.invenno'),
			'photo'=>array('name'=>'pname','picpath_s','picpath_m','_on'=>'invenpic.photoid=photo.id'),
	);
}