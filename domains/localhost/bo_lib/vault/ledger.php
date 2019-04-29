<?php

Class Ledger(){
    private $context;
    
    function __construct($c){
        $this->context=$c;
    }

    public function getBalance($type,$vaultid){
        $redis = $context->resolve("redis:get");
        $input=new stdClass();
        $input->key="vault-".$request->Params()->vaultid."-".$type;
        $cache=$redis->get($input->key);
        if(isset($cache)){
            return floatval($cache);
        }else{
            return $this->getDBbalance($type,$vaultid);
        }
    }

    private function getDBbalance( $type,$vaultid){
        $sqlUnit = $context->resolve("mssql:query");
        switch($type){
            case "lco":
                $sql="SELECT (Balance-BlockAmount) As Balance FROM a_CODealerVault  WHERE GULCOID = '".$vaultid."'";
                $dbobj= $sqlUnit->process($sql);
                if($dbobj){
                    return floatval($dbobj[0]->Balance);
                }else{
                    throw new Exception('has No Balance For the provided Main Valutid '.$vaultid);
                }
                break;
            case "acccount":
                $sql="SELECT (Balance-BlockAmount) As Balance FROM a_CustomerVault  WHERE guaccountid = '".$vaultid."'";
                $dbobj= $sqlUnit->process($sql);
                if($dbobj){
                    return floatval($dbobj[0]->Balance);
                }else{
                    throw new Exception('has No Balance For the provided Valutid '.$vaultid);
                }
                
                break;
            default:
                throw new Exception('No Access to this vault '.$type);
                break;
        }
    
    }

}

?>