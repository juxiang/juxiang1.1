<?php

class ShowAllAction extends CommonAction {
    
    public function showDetail() {
        $goodsCat = M("Organization")->order("id")->select();
        $array = array();
        foreach ($goodsCat as $val) {
            $array[$val['id']] = $val['title'];
        }
        $this->gdCat = $array;
        $model = M('PhoneAdmin');
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function index (){
        if($_GET['key']!=null){
            $key = $_GET['key'];
            $_SESSION['searchKey']=$key;
        }
        $key=$_SESSION['searchKey'];
        $waitList = array();
        array_push($waitList, $key);
        $rsList = array();
        
        
        while (count($waitList) > 0) {
            $tmp = array_pop($waitList);
            array_push($rsList, $tmp);

            $dc = M("Organization");
            $dcon['pid'] = $tmp;
            $dcs = $dc->where($dcon)->field('id')->select();

            foreach ($dcs as $value) {
                array_push($waitList, $value['id']);
            }
        }
        
        $con = parent::_search('PhoneAdmin');
        $con['cat_id'] = array('in', $rsList);
           //dump($con);     
        $list = M("PhoneAdmin")->where($con)->order("cat_id,inner_seq")->select();
        $this->assign('list', parent::GetCurPage($list));
     
        
        //p($list);die;
        $this->display();
    } 

}
?>