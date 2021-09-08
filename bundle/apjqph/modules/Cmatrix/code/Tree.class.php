<?php
/**
 * Класс \Cmatrix\Tree
 * 
 * Манипулирование деревом
 * 
 * @author ura ura@itx.ru
 * @version 1.0 2019-05-16
 */
namespace Cmatrix;

class Tree {
    static $INSTANCES = [];
    private $SrcTree;
    
    private $Parent = null;
    
    private $NameName = 'name';
    private $ParentName = 'parent';
    
    private $_Tree;
    private $_Plaintree;
    
    // --- --- --- --- --- --- ---
    function __construct(array $tree){
        $this->SrcTree = $tree;
    }
    
    // --- --- --- --- --- --- ---
    function __get($name){
        switch($name){
            case 'Roots' : return $this->getMyRoots();
            case 'Tree' : return $this->getMyTree();
            case 'PlainTree' : return $this->getMyPlaintree();
            case 'ChildrenAll' : return $this->ChildrenAll();
        }
    }
    
    // --- --- --- --- --- --- ---
    private function getMyRoots(){
        if($this->_Roots) return $this->_Roots;
        
        //$Roots = array_filter($this->SrcTree,function($value){ return !$value[$this->ParentName] ? true : false; });
        $Roots = array_filter($this->SrcTree,function($value){ return $value[$this->ParentName] == $this->Parent ? true : false; });
        $Roots = array_map(function($value){ return $value[$this->NameName]; },$Roots);
        
        return $Roots;
    }

    // --- --- --- --- --- --- ---
    /**
     * Функция getMyTree()
     * 
     * Исходные данные
     * [
     *      [ 'name'=> 'root1',        'parent' => null ],
     *      [ 'name'=> 'root2',        'parent' => null ],
     *      [ 'name'=> 'root3',        'parent' => null ],
     *      [ 'name'=> 'root4',        'parent' => null ],
     *      [ 'name'=> 'children11',   'parent' => 'root1' ],
     *      [ 'name'=> 'children12',   'parent' => 'root1' ],
     *      [ 'name'=> 'children121',  'parent' => 'children12' ],
     *      [ 'name'=> 'children122',  'parent' => 'children12' ],
     *      [ 'name'=> 'children123',  'parent' => 'children12' ],
     *      [ 'name'=> 'children1231', 'parent' => 'children123' ],
     *      [ 'name'=> 'children1232', 'parent' => 'children123' ],
     *      [ 'name'=> 'children41',   'parent' => 'root4' ],
     *      [ 'name'=> 'children42',   'parent' => 'root4' ],
     *      [ 'name'=> 'children43',   'parent' => 'root4' ]
     * ]
     * 
     * Результат:
     * [
     *     'root1' => [
     *         'children11' = null,
     *         'children12' = [
     *             'children121' = null,
     *             'children122' = null,
     *             'children123' = [
     *                 'children1231' = null,
     *                 'children1232' = null,
     *             ],
     *         ],
     *     ],
     *     'root2' => null,
     *     'root3' => null,
     *     'root4' => [
     *         'children41' = null,
     *         'children42' = null,
     *         'children43' = null,
     *     ],
     * ]
     */ 
    private function getMyTree(){
        if($this->_Tree) return $this->_Tree;
        
        $Roots = $this->Roots;
        
        $_rec = function($root) use(&$_rec){
            $Arr = [];
            $Children = array_filter($this->SrcTree,function($node) use($root){ return $node[$this->ParentName] === $root ? true : false; });
            array_map(function($node) use(&$Arr,$_rec){
                $name = $node[$this->NameName];
                $Arr[$name] = $_rec($name);
            },$Children);
            return count($Arr) ? $Arr : null;
        };
        
        $Tree = [];
        foreach($Roots as $root){
            $Tree[$root] = $_rec($root);
        }
        return $this->_Tree = $Tree;
    }
    
    // --- --- --- --- --- --- ---
    /**
     * Функция getMyPlaintree
     * 
     * Формирует сортированный по иерархии список узлов
     */
    private function getMyPlaintree(){
        if($this->_Plaintree) return $this->_Plaintree;
        
        $Plaintree = [];
        $_rec = function($tree) use(&$Plaintree,&$_rec){
            foreach($tree as $node=>$children){
                $Plaintree[] = $node;
                if($children) $_rec($children);
            }
        };
        
        $_rec($this->Tree);
        
        return $this->_Plaintree = $Plaintree;
    }
    
    // --- --- --- --- --- --- ---
    /**
     * Функция ChildrenAll
     * 
     * Формирует массив всех наследников
     * 
     * Исходные данные
     * [
     *      [ 'name'=> 'root1',        'parent' => null ],
     *      [ 'name'=> 'root2',        'parent' => null ],
     *      [ 'name'=> 'root3',        'parent' => null ],
     *      [ 'name'=> 'children11',   'parent' => 'root1' ],
     *      [ 'name'=> 'children12',   'parent' => 'root1' ],
     *      [ 'name'=> 'children121',  'parent' => 'children12' ],
     *      [ 'name'=> 'children122',  'parent' => 'children12' ],
     *      [ 'name'=> 'children1221', 'parent' => 'children122' ],
     *      [ 'name'=> 'children1222', 'parent' => 'children122' ],
     *      [ 'name'=> 'children21',   'parent' => 'root2' ],
     * ]
     * 
     * Результат:
     * [
     *     'root1' => [
     *         'children11',
     *         'children12',
     *         'children121',
     *         'children122',
     *         'children1221',
     *         'children1222',
     *     ],
     *     'root2' => [
     *         'children21',
     *     ],
     *     'children12' => [
     *         'children121',
     *         'children122',
     *         'children1221',
     *         'children1222',
     *     ],
     *     'children122' => [
     *         'children1221',
     *         'children1222',
     *     ]
     * ]
     */
    private function ChildrenAll(){
        //dump($this->Roots);
        $Values = [];
        
        $_rec = function($parent,$root=null) use(&$Values,&$_rec){
            $root = $root ? $root : $parent;
            
            array_map(function($value) use($parent,$root,&$Values,&$_rec){
                //dump($value[$this->ParentName],$parent);
                if($value[$this->ParentName] === $parent){
                    $Values[] = [
                        $this->ParentName => $root,
                        $this->NameName => $value[$this->NameName],
                    ];
                    $_rec($value[$this->NameName],$root);
                }
                
            },$this->SrcTree);
        };
        
        if($this->Parent) $_rec($this->Parent);
        else array_map(function($value) use($_rec){
            $_rec($value[$this->NameName]);
        },$this->SrcTree);
        
        return $Values;
    }
    
    // --- --- --- --- --- --- ---
    // --- --- --- --- --- --- ---
    // --- --- --- --- --- --- ---
    /**
     * Функция setNameName
     * Установка имени поля имени
     */
    public function setNameName($value){
        $this->NameName = $value;
        return $this;
    }
    
    // --- --- --- --- --- --- ---
    /**
     * Функция setParentName
     * Установка имени поля родителя
     */
    public function setParentName($value){
        $this->ParentName = $value;
        return $this;
    }

    // --- --- --- --- --- --- ---
    /**
     * Функция setParent
     * Установка родителя, от которого считать дерево
     */
    public function setParent($value=null){
        $this->Parent = $value;
        return $this;
    }
    
    // --- --- --- --- --- --- ---
    // --- --- --- --- --- --- ---
    // --- --- --- --- --- --- ---
    static function instance(array $tree){
        $key = md5(serialize($tree));
        if(isset(self::$INSTANCES[$key])) return self::$INSTANCES[$key];
        else return self::$INSTANCES[$key] = new self($tree);
    }
}
?>