<?php
Class lcoLedger{
    private $sqlCon;
    function __construct($sqlCon) {
        $this->sqlCon=$sqlCon;
    }

    public function GetBalance($lcocode){
        //if()
        
        $sql="SELECT Top (1) OpeningBalance,TranDateTime FROM a_LCOLedger  WHERE GULCOID = '".$this->GetGULCOID($lcocode)."' ORDER BY ID DESC";
        $dbobj= $this->sqlCon->getQuery($sql);
        //var_dump($sql);
        if(count($dbobj)>0){
            return $dbobj[0]->OpeningBalance;
        }else{
            return 0;
        }
    }

    private function GetGULCOID($lcocode){
        $sql="SELECT GULCOID FROM m_CODealerMaster  WHERE LCOCode = '".$lcocode."'";
        $dbobj= $this->sqlCon->getQuery($sql);
        if(count($dbobj)>0){
            return $dbobj[0]->GULCOID;
        }else{
            throw new Exception('LCO was not found');
        }

    }
} 
?>