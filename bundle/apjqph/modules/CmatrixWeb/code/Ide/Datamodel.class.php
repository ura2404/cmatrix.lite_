<?php
namespace CmatrixWeb\Ide;
use \Cmatrix as cm;
use \Cmatrix\Exception as ex;
use \CmatrixDb as db;
use \CmatrixWeb as web;

class Datamodel{
    static $INSTANCES = [];
    private $Datamodel;
    private $P_Total;
    private $P_Pager;

    // --- --- --- --- ---
    function __construct($url){
        $this->Datamodel = cm\Ide\Datamodel::instance($url);
    }
    
    // --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Code' : return $this->Datamodel->Url;
            case 'Name' : return $this->Datamodel->Name;
            case 'Props' : return $this->getMyProps();
            case 'OwnProps' : return $this->getMyOwnProps();
            case 'Lines' : return $this->getMyLines();
            case 'Total' : return $this->getMyTotal();
            case 'Sorts' : return $this->getMySorts();
            case 'Pager' : return $this->getMyPager();
            case 'Rfilter' : return $this->getMyRfilter();
            case 'Pfilter' : return $this->getMyPfilter();
            default : throw new ex\Property($this,$name);
        }
    }
    
    // --- --- --- --- ---
    private function getMyOwnProps(){
    }
    
    // --- --- --- --- ---
    private function getMyProps(){
        $Props = $this->Datamodel->OwnProps;
        
        $_align = function($code,$prop){
            switch($code){
                case 'status' : return 'center';
            }
            
            switch($prop['type']){
                case '::id::' :
                case 'integer' :
                case 'real' :
                    return 'right';
                        
                case '::hid::' :
                case 'bool' :
                case 'timestamp' :
                    return 'center';
                
                default : return 'left';
            }
        };
        
        array_map(function($code,$prop) use(&$Props,$_align){
            $prop['name'] = $prop['name'] ? cm\Lang::str($prop['name']) : $prop['code'];
            $prop['label'] = $prop['label'] ? cm\Lang::str($prop['label']) : ($prop['name'] ? cm\Lang::str($prop['name']) : $prop['code']);
            $prop['baloon'] = $prop['baloon'] ? cm\Lang::str($prop['baloon']) : null;
            $prop['sortable'] = true;
            $prop['align'] = $_align($code,$prop);
            //$prop['sort'] = !isset($Sort[$code]) ? 's' : $Sort[$code];
            //$prop['hsort'] = $this->getHsort($code,$prop);
            
            $Props[$code] = $prop;
        },array_keys($Props),array_values($Props));
        
        $Props = array_merge([
            'row_index'=>[
                'code' => 'row_index',
                'type' => '::index::',
                'baloon' => 'Выбрать все записи',
                'sortable' => false,
                'align' => 'center'
            ]
        ],$Props);
        
        return $Props;
    }
    
    // --- --- --- --- ---
    private function getMySorts(){
        $_calculate = function(){
            $Sort = web\Page::instance()->getParam('s');
            if(!$Sort) return [];
            
            $Arr = explode(',',$Sort);
            $Count = count($Arr);
            $Even = $Count % 2;
            if($Even) return;
            
            $Sort = [];
            for($i=0;$i<count($Arr)-1;$i=$i+2){
                $Order = $Arr[$i+1];
                $Sort[$Arr[$i]] = $Arr[$i+1];
            }
            return $Sort;
        };
        $Sorts = $_calculate();
        
        // ---  ссылка на следующую сортировку
        // обязательно разбирать массив Sorts для сохранения последовательности сортировки по полям
        $_href = function($code,$sort) use($Sorts){
            $Next = $sort === 's' ? 'a' : ($sort === 'a' ? 'd' : 's');
            if(isset($Sorts[$code]) && $Next === 's') unset($Sorts[$code]);
            else $Sorts[$code] = $Next;
            
            return web\Page::instance()->setParam('s',implode(',',array_map(function($code,$sort){
                return $code .','. $sort;
            },array_keys($Sorts),array_values($Sorts))))->getUrl();
        };
        
        $Arr = [];
        array_map(function($code,$prop) use(&$Arr,$Sorts,$_href){
            if(!$prop['sortable']) return;
            
            if(!isset($Sorts[$code])) $Arr[$code] = [ 's', $_href($code,'s') ];
            else $Arr[$code] = [ $Sorts[$code], $_href($code, $Sorts[$code]) ];
        },array_keys($this->Props),array_values($this->Props));
        
        return $Arr;
    }

    // --- --- --- --- ---
    /**
     * Праметр pager в url строкае 'p=<count>,<offset>'
     * 
     * [
     *     'total' - всего строк
     *     'pages' - коло-во страниц
     *     'page' - номер текущей страницы
     *     'count' - кол-во строк на странице
     *     'current' - страница из страниц
     * ]
     */
    private function getMyPager(){
        if($this->P_Pager) return $this->P_Pager;
        
        $_calculate = function(){
            $Pager = web\Page::instance()->getParam('p');
            if(!$Pager) $Pager = '10,0';
            
            $Arr = array_slice(array_filter(explode(',',$Pager),function($value){ return !!$value; }),0,2);
            if(count($Arr) !=2) $Arr[1] = 0;
            
            return $Arr;
        };
        
        // функция поиска номера страницы по номеру строки
        $_searchPage = function($count,$offset,$pages,$total){
            // массив номеров строк начала и конца страниц
            $PagesLines = array_map(function($page) use($count,$total){
                $First = $page * $count;
                $Last = ($Last = $page * $count + $count - 1) > $total-1 ? $total-1 : $Last;
                return [ $First,$Last ];
                
            },range(0,$pages-1));
            
            
            $Page = 0;
            foreach($PagesLines as $index=>$page){
                if($offset >= $page['0'] && $offset <= $page['1']) {$Page = $index; break;}
            }
            return $Page;
        };
        
        // --- --- --- --- ---
        $Pager = $_calculate();
        $Count = $Pager[0];
        $Offset = $Pager[1];
        $Total = $this->Total;
        $Pages = ceil($Total / $Count);
        $Page = $_searchPage($Count,$Offset,$Pages,$Total);

        if($Page == 0) $Hfirst = $Hprev = null;
        else{
            $Hfirst = web\Page::instance()->setParam('p',$Count.',0')->getUrl();
            $Hprev = web\Page::instance()->setParam('p',$Count.','.(($Page-1)*$Count))->getUrl();
        }
        
        if($Page == $Pages - 1) $Hlast = $Hnext = null;
        else{
            $Hlast = web\Page::instance()->setParam('p',$Count.','.(($Pages-1)*$Count))->getUrl();
            $Hnext = web\Page::instance()->setParam('p',$Count.','.(($Page+1)*$Count))->getUrl();
        }
        
        $Pager = [
            'total' => $Total,
            'pages' => $Pages,
            'page' => $Page,
            'count' => $Count,
            'current' => $Page .'/'. $Pages,
            'hfirst' => $Hfirst,
            'hprev' => $Hprev,
            'hnext' => $Hnext,
            'hlast' => $Hlast
        ];
        
        return $this->P_Pager = $Pager;
    }    
    
    // --- --- --- --- ---
    /**
     * Получить параметрический фильтр
     */
    private function getMyPfilter(){
        $Filter = web\Page::instance()->getParam('f');
        $Clean = strtr($Filter, ' ', '+');
        $Res = json_decode(base64_decode( $Clean ),true);
        //dump($Res);
        
        return $Res;
    }
    
    // --- --- --- --- ---
    /**
     * Получить свободный фильтр
     */
    private function getMyRfilter(){
        $Filtr = web\Page::instance()->getParam('r');
        return $Filtr;
    }

    // --- --- --- --- ---
    private function getMyLines(){
        $Sorts = array_filter(array_map(function($sort){
            return $sort[0];
        },$this->Sorts),function($sort){
            return $sort !== 's';
        });
        
        $Limit = array_intersect_key($this->Pager,array_flip(['count','page']));
        
        $Query = db\Cql::select($this->Datamodel)->orders($Sorts)->rules()->limit($Limit);
        //dump($Query->Query);
        
        $Res = db\Connect::instance()->query($Query);
        
        $Pager = $this->Pager;
        $Iterator = $Pager['page'] * $Pager['count'];
        $Res = array_map(function($tr) use(&$Iterator){
            array_map(function($code,$td) use(&$tr){
                $Type = $this->Datamodel->Props[$code]['type'];
                if($Type === 'timestamp') $td = strBefore($td,'.');
                elseif($Type === 'bool'){
                    //return $td === true ? ''
                    //return $td;
                }
                return $tr[$code] = $td;
            },array_keys($tr),array_values($tr));
            
            return array_merge(['row_index' => ++$Iterator ],$tr);
        },$Res);
        
        //dump($Res);
        
        return $Res;
    }
    
    // --- --- --- --- ---
    private function getMyTotal(){
        if($this->P_Total) return $this->P_Total;
        
        $Query = db\Cql::select($this->Datamodel)->prop('count::id','qaz');
        $Res = db\Connect::instance()->query($Query,\PDO::FETCH_NUM);
        
        if(!$Res) return 0;
        return $this->P_Total = array_values($Res)[0][0];
    }
    
    // --- --- --- --- ---
    // --- --- --- --- ---
    // --- --- --- --- ---
    static function instance($url){
        $Key = $url;
        if(array_key_exists($Key,self::$INSTANCES)) return self::$INSTANCES[$Key];
        return new self($url);
    }
}
?>