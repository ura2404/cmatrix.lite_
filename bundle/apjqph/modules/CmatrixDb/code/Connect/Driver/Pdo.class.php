<?php
namespace CmatrixDb\Connect\Driver;
use \Cmatrix as cm;
use \CmatrixDb as db;
use \Cmatrix\Exception as ex;

class Pdo extends db\Connect\Driver implements db\Connect\iDriver{
    private $Dbh = null;
    
    // --- --- --- --- ---
    function __construct(array $config, db\Connect\iDatabase $database){
		try{
            $dsn = [];
            $dsn[] = 'host='.$database->Host;
            $dsn[] = 'port='.$database->Port;
            
            // создание коннекта возможно без имени БД (например для создания БД)
            if($database->Name) $dsn[] = 'dbname='.$database->Name;
            
            $dsn[] = 'user='.$database->User;
            $dsn[] = 'password='.$database->Pass;
            
            $dsn = $database->Type .":". implode(' ',$dsn);
            
			$this->Dbh = new \PDO($dsn,$database->User,$database->Pass);
			//$this->Dbh = new \PDO($dsn,$database->User,$database->Pass,[\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION]);
			//    PDO::ATTR_ERRMODE - 
			//    PDO::ERRMODE_EXCEPTION - 
			//    PDO::ATTR_PERSISTENT => true     //постоянное соединение
		}
		catch (\PDOException $e) {
			throw new ex(get_class($this).' error: pdo instance is not created. ' . $e->getMessage());
		}
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function prepareQuery($query){
        $Query = is_array($query) ? array2line($query) : [$query];
        $Query = implode(";\n",$Query).';';
        //$Query = preg_replace("#( |\s) *#i"," ",$Query);
        $Query = preg_replace("#( |\r) *#i"," ",$Query);
        return $Query;
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function query($query,$mode=\PDO::FETCH_ASSOC){
        $Query = $this->prepareQuery($query);
		if(!strlen($Query)) return;
			
        $this->Dbh->beginTransaction();
        
		$Res = $this->Dbh->query($Query);
        //dump($Res);
        
        if($Res === FALSE){
            $this->Dbh->rollback();
			//$error = get_class($this)." error: error while running query [ ".str_replace("\n"," ",$q)." ].".PHP_EOL;
			$Error = $this->Dbh->errorInfo()[2];
			throw new ex($Error);
        }
        
        $this->Dbh->commit();
        return $Res->fetchAll($mode);
    }
    
    // --- --- --- --- ---
    public function exec($query){
        $Query = $this->prepareQuery($query);
        //dump(explode("\n",$Query));die();
        
		if(!strlen($Query)) return;
			
        $this->Dbh->beginTransaction();
        
		$Res = $this->Dbh->exec($Query);
		//dump($Res);

        if($Res === FALSE){
            $this->Dbh->rollback();
			//$error = get_class($this)." error: error while running query [ ".str_replace("\n"," ",$q)." ].".PHP_EOL;
			$Error = $this->Dbh->errorInfo()[2];
			throw new ex($Error);
        }
        
        $this->Dbh->commit();
        return $Res;
    }
}
?>
