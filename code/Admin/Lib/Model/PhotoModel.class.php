<?php
class PhotoModel extends RelationModel{
	protected $tableName="Photo";
	protected $_link = array(
		'Album' =>array(
				'mapping_type' =>BELONGS_TO,
				'class_name' =>'album',
				'foreign_key' =>'aid',
				'relation_foreign_key'=>'pid',
				'mapping_name' =>'albums',
				'mapping_fields' =>'title,sort,status',
				'as_fields' =>'title,asort,astatus',
		)
	);
}