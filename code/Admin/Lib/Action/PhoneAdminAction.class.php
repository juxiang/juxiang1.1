<?php

class PhoneAdminAction extends CommonAction {
    
    public function index (){
        $con = parent::_search('PhoneAdmin');      
        $list = M("PhoneAdmin")->where($con)->order("cat_id,inner_seq")->select();

       
        
        $this->assign('list', parent::GetCurPage($list));
        $this->display();
    } 
    
    public function edit (){
        $og=M("Organization")->order("id")->select();
        $array=array();
        foreach($og as $val){
            $array[$val['id']]=$val['title'];
        }
        $this->gdCat=$array;
        parent::edit();
    } 
    
    public function treeLookup() {
        $gd = M("Organization");
        $menu = $gd->field('id,pid,title')->select();
        $menu = arrToTree($menu, 0);
        $this->assign('menu', $menu);
        $this->display();
    }

}
?>