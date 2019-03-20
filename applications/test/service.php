<?php

class SearchServices {
    public function getaccount($req){
        require_once (PLUGIN_PATH . "/SQLDB/connection.php");
        $sql = new SQLDataConnector();
        $sql->Open("dentest");
        $dbobj= $sql->getQuery("select top 100 * from a_CustAccountInformation where Accountno=".$_GET["accno"]);
        $sql->Close();
        return $dbobj;
    }

    public function postq($req){
        
        $sall=$req->Body(true);
        $f=new stdClass();
        foreach($sall as $s){
            $user=null;
            if(isset($req->headers()->rhost)){
                $user= Auth::AutendicateDomain($req->headers()->rhost,$req->headers()->sosskey,$s->storename,"query");
                if(!isset($user->userid)){
                    $err=new stdClass();
                    $err->Mesaage="Error Authendicating User Autherize to this object store";
                    $f->{$s->storename}=$err;
                    continue;
                }
            }
            $result= CacheData::getObjects(md5($s->search),$s->storename);
            if(!isset($result)){
                $result=null;
                if($s->search!=""){
                    $result = SOSSData::Query ($s->storename,urlencode($s->search));
                }else{
                    $result = SOSSData::Query ($s->storename,null);
                }
                if($result->success){
                    $f->{$s->storename}=$result->result;
                    if(isset($result->result)){
                        if($s->search==""){
                            $s->search="all";
                        }
                        CacheData::setObjects(md5($s->search),$s->storename,$result->result);
                    }
                }else{
                    $f->{$s->storename}=$result;
                }
            }else{
                $f->{$s->storename}= $result;
            }
            
        }
        return $f;
    }

    public function getattribute($req){
        
    }
}

?>