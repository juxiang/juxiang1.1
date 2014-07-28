<?php
class AlbumModel extends RelationModel{
	protected $tableName="Album";
	protected $_link = array(
		'Photo' =>array(
				'mapping_type' =>HAS_MANY,
				'class_name' =>'photo',
				'foreign_key' =>'aid',
				'mapping_name' =>'photo',
				'mapping_fields' =>'name,picpath,sort,status',
				'as_fields' =>'pname,picpath,psort,pstatus',
	)
	);
}