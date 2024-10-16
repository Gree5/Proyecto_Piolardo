<?php 
class PDO_SERVER {
    private $PDO;
    public $SP = array();
    private $nameSP = '';
    private $sptext = '';

    public function __construct(){

    }


    public function SPname($nameSP,$login){

        $this->nameSP = $nameSP;

        $host = $login['host'];
        $dbname = $login['dbname'];
        $user = $login['user'];
        $pass = $login['pass'];
        $port = $login['port'];
        $this->SP = array();
            

        $this->PDO = new PDO("mysql:host=$host;port=$port;dbname=$dbname",
                "$user",
                "$pass",
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    
                    1002 => "SET NAMES utf8")
                );
        
    }

    public function SP_type($v,$type){


        
        switch (strtoupper($type)) {
            case 'INT':
                if ($v == ''){
                    $v = "null";
                }else{
                    $v = $v;    
                }
                
                break;
            case 'CHAR':
                if ($v == ''){
                    $v = '""';
                }else{
                    
                    $v = '"'.addcslashes($v,'"').'"';

                }
                break;
            case 'DATE':
                if ($v != ''){
                    $f = explode('/', $v);
                    $v = "'".$f[2] . '-' . $f[1] . '-' . $f[0]."'";    
                }else{
                    $v = "null";
                }
                
                break;
        }
        $this->SP[] = $v;
        
    }
    
    public function SP_text(){
        $sptext = " CALL ".$this->nameSP."(";

        if(is_countable($this->SP)?count($this->SP):0 >0){
            foreach ($this->SP as $value){
                $sptext.=$value.",";    
            }
        }  
        $len = strlen($sptext);
        if(is_countable($this->SP)?count($this->SP):0 >0) {
            $len-=1;
        }
        $this->sptext = substr($sptext, 0, $len).");";
        
    }
    public function ExeSP(){
        $this->SP_text();
        $stmt = $this->PDO->prepare($this->sptext);
        $stmt -> execute();
        $var = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        $stmt-> closeCursor();
        return $var;
    }

    public function unsetPDO(){        
        unset ($this->sptext);
        unset ($this->SP);
        unset ($this->nameSP);
    }
    public function getCall(){
        $this->SP_text();
        return $this->sptext;
    }

  
}