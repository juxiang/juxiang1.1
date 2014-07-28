<?php
class AlbumAction extends CommonAction{
	public function index() {
		$con = parent::_search('Album');
		$al = M("Album");
		$pList=$al->where($con)->field('id,pid,title')->order('pid,sort')->select();
		if(!empty($con)){
			$list=$pList;
		}else{
			$catList = $al->field('id,pid,title')->order('pid,sort')->select();
			$list = outTreeList(arrToTree($catList,0),0);
		}
		$this->assign('list', parent::GetCurPage($list));
		$this->display();
	}	
	public function _before_add() {
		$pid = 0;
		if ($_GET['pid'] != '') {
			$pid = $_GET['pid'];
		}
		$this->assign('pid', $pid);
	}
	public function foreverdelete() {
		$model = M("Album");
		if (!empty($model)) {
			$id = $_REQUEST ['id'];
			if (isset($id)) {
				$con['pid'] = $id;
				if ($model->where($con)->count() == 0) {
					$con2['id'] = $id;
					if (false !== $model->where($con2)->delete()) {
						$this->success('删除成功！');
					} else {
						$this->error('删除失败！');
					}
				} else {
					$this->error('请先删除子类！');
				}
			} else {
				$this->error('非法操作');
			}
		}
	}
}