<?php 
class GetnoModel extends Model
{
	public $getno;
	
	public function _By_Type($att,$val)
	{
		$db = M();
		$sql="select * from `wx_getno_by_type` where attno='$att'";			
			$arr = $db->query($sql);			
			if($arr)
			{	
				$sql="update `wx_getno_by_type` set vals=vals+1 where attno='$att' ";
				$db->execute($sql);
				$getno= $arr[0]["vals"]+1;
				
			}
			else
			{
				$sql="insert into `wx_getno_by_type`(attno,lens,vals) values ('$att','$val','$val') ";
				
				$db->execute($sql);
				$getno= $val;
			}
			return  $getno;
				
	}
	
	public function _By_Date($att,$date,$val)
	{
		$db = M();
		
		$sql="select * from `getno_by_date` where attno='$att' and cdate='$date' ";
		$arr = $db->query($sql);
		if($arr)
		{
			$sql="update `getno_by_date` set vals=vals+1 where attno='$att' and cdate='$date' ";
			$db->execute($sql);
			$getno= $arr[0]["vals"]+1;
		}
		else
			{
			$sql="insert into `getno_by_date`(attno,cdate,lens,vals) values ('$att','$date','$val','$val') ";
			$db->execute($sql);
			$getno= $val;
		}
			
	}
}
?>