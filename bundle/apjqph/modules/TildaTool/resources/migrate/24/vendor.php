<?php
use \Cmatrix as cm;
use \CmatrixDb as db;

include '../../../../../defs.php';
include '../../../../../common.php';

$Connect2 = db\Connect::get('dbv2');
$Connect4 = db\Connect::get();

// --- --- --- --- ---
$Table2 = 'mz_view_tl_vendor';
//id	hid	active	deleted	create_ts	upd_ts	entity_parent_id	type_id	session_id	session_upd_id	sysgroup_id	sysuser_id	info	tag	status_id	ordd	name
$Props2 = [ 'id','hid','name','info' ];
 
$Query2 = [];
$Query2[] = 'SELECT ' . implode(',',$Props2);
$Query2[] = 'FROM ' . $Table2;
$Query2[] = 'WHERE active is TRUE';
$Query2[] = 'ORDER BY ordd';
$Query2 = implode(' ',$Query2);

$Data2 = $Connect2->query($Query2);
$Data2 = array_combine(array_column($Data2,'hid'),$Data2);

// --- --- --- --- ---
$Props4 = [ 'id','hid','name','info' ];
$Query4 = db\Cql::select('TildaTool/Vendor')->props($Props4);

$Data4 = $Connect4->query($Query4);
$Data4 = array_combine(array_column($Data4,'hid'),$Data4);

//dump($Data2);
//dump($Data4);

// --- --- --- --- ---
// --- OLD ---
$Old = array_intersect_key($Data4,$Data2);
//dump($Old);

// --- --- --- --- ---
// --- NEW ---
$New = array_diff_key($Data2,$Data4);
//dump($New);

$Query = db\Cql::insert('TildaTool/Vendor')->valuesArr($New);
dump($Query->Query);

?>