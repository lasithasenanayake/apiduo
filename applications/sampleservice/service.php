<?php
require_once(PLUGIN_PATH . "/sossdata/SOSSData.php");
require_once(PLUGIN_PATH . "/phpcache/cache.php");
require_once(PLUGIN_PATH . "/auth/auth.php");

class SearchServices {
    public function postqcrossdomain($req){
        $body=$req->Body(true);
        //$f=new ();
        if(!isset($body->domain))
            $body->domain=MAIN_STORE_DOMAIN;
        $sall=$body->query;
        $data=Auth::CrossDomainAPICall($body->domain,"/components/dock/soss-data/service/q","POST",$body->query);
        return $data->result;
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