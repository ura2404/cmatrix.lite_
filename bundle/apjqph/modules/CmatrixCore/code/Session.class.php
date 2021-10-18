<?php
namespace CmatrixCore;
use \Cmatrix as cm;
use \CmatrixDb as db;
use \Cmatrix\Exception as ex;

class Session {
    static $INSTANCES = [];

    private $CookeiName;
    private $Hid;
    private $Session;
    private $Sysuser;
    
    // --- --- --- --- ---
    function __construct(){
        $this->check();
    }

    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Hid' : return $this->Hid;
            case 'Session' : return $this->Session;
            case 'Sysuser' : return $this->Sysuser;
            default : throw new ex\Property($this,$name);
        }
    }

    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    private function check(){
        if(cm\App::instance()->Sapi === 'CLI'){
        }
        else{
            $this->CookieName = str_replace('.','_',cm\Hash::getFile(CM_TOP.'/config.json')->getValue('project/code','cmatrix'));
            $this->Hid = empty($_COOKIE[$this->CookieName]) ? null : $_COOKIE[$this->CookieName];
            
            // нет куки, новая сессия
            if(!$this->Hid){
                //dump('новая сессия');
                $this->Hid = hid();
                $Ident = $this->getIdent();
                
                $Ob = db\Obbject::instance('/CmatrixCore/Session')->get($Ident);
                if(!$Ob->IsEmpty){
                    // лишние активные сессии удалить
                    $Ob->history(true)->delete();
                }
                
                db\Obbject::instance('/CmatrixCore/Session')->create(array_merge($Ident,['hid'=>$this->Hid]));

                $this->setCookie($this->Hid);
            }
            // какая-то старая сессия
            else{
                //dump('старая сессия');
                
                $Ident = $this->getIdent($this->Hid);
                
                $Session = db\Obbject::instance('/CmatrixCore/Session')->active(true)->get($Ident);
                if($Session->IsEmpty){
                    // если в БД нет этой сессии, то это или сессия закрыта, или какой-то сбой, обновить сессию
                    $this->unsetCookie($this->Hid);
                }
                else{
                    $this->Session = $Session;
                    $this->Sysuser = db\Obbject::instance('/CmatrixCore/Sysuser')->get($Session->Data['sysuser_id']);
                }
            }
        }

        $this->touch();
    }
    
    // --- --- --- --- ---
    private function getIdent(){
        $Arr = [];
        
        if(cm\App::instance()->Sapi === 'CLI'){
            $Arr['ip4'] = '0.0.0.0';
            $Arr['agent'] = 'Cmatrix local';
        }
        else{
            $Arr['ip4']      = isset($_SERVER['REMOTE_ADDR'])          ? $_SERVER['REMOTE_ADDR']          : NULL;
            $Arr['ip4x']     = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : NULL;
            $Arr['proxy']    = isset($_SERVER['HTTP_VIA'])             ? $_SERVER['HTTP_VIA']             : NULL;
            $Arr['agent']    = isset($_SERVER['HTTP_USER_AGENT'])      ? $_SERVER['HTTP_USER_AGENT']      : NULL;
            // $Arr['lang']     = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : NULL;
            // $Arr['charset']  = isset($_SERVER['HTTP_ACCEPT_CHARSET'])  ? $_SERVER['HTTP_ACCEPT_CHARSET']  : NULL;
            // $Arr['encoding'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : NULL;
            
            if($Arr['ip4'] === '::1') $Arr['ip4'] = '127.0.0.1';
        }
        return $Arr;
    }
    
    // --- --- --- --- ---
    private function setCookie($hid){
        //$Path = cm\Hash::getFile(CM_TOP.'/config.json')->getValue('www/root');
        $Path = '/';
        
        // --- выставить куку на год
        $Days = 1;    // кол-во дней для куки
        setcookie($this->CookieName,$hid,time() + ($Days * 86400),$Path);
        setcookie($this->CookieName.'_ts',time(),time() + ($Days * 86400),$Path);
			
		// --- перегрузить страницу
		header("Refresh:0");
		exit();        
    }

    // --- --- --- --- ---
    private function unsetCookie(){
        //$Path = cm\Hash::getFile(CM_TOP.'/config.json')->getValue('www/root');
        $Path = '/';
        
        // --- выставить куку на год
        $Days = 1;    // кол-во дней для куки
        setcookie($this->CookieName,'',time()-3600,$Path);
        setcookie($this->CookieName.'_ts','',time()-3600,$Path);
			
		// --- перегрузить страницу
		header("Refresh:0");
		exit();
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    protected function dbLogin($user,$pass){
        if(($User = db\Obbject::instance('/CmatrixCore/Sysuser')->get([
            'code' => $user,
            'pass' => $pass
        ]))->IsEmpty) throw new cm\Exception('Неверная комбинация имени и пароля.');
        
        if(cm\App::instance()->Sapi === 'CLI'){
        }
        else{
            $this->Session->history(false)->update(['sysuser_id'=>$User->Data['id']]);
            //$Ob = db\Obbject::instance('/CmatrixCore/Session')->get($Ident);
        }
        
        //$this->touch();
    }

    // --- --- --- --- ---
    protected function dbLogout(){
        $this->unsetCookie();
    }

    // --- --- --- --- ---
    protected function touch(){
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    public function login($user,$pass){
        if(cm\App::instance()->isDb) $this->dbLogin($user,$pass);
        else throw new \Exception('Система авторизации неактивна.');
    }

    // --- --- --- --- ---
    public function logout(){
        if(cm\App::instance()->isDb) $this->dbLogout();
        else throw new \Exception('Система авторизации неактивна.');
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance(){
        $Key = md5('current');
        if(isset(self::$INSTANCES[$Key])) return self::$INSTANCES[$Key];
        
        return self::$INSTANCES[$Key] = new self;
    }
}
?>