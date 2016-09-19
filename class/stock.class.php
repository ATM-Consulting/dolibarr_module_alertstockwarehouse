<?php

class TStock extends TObjetStd {

	var $exist = false;

	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'alert_by_stock');
		parent::add_champs('code', array('index'=>true) );
		parent::add_champs('fk_product', array('type'=>'integer', 'index'=>true));
		parent::add_champs('fk_entrepot', array('type'=>'integer', 'index'=>true));
		parent::add_champs('limite', array('type'=>'integer'));
		
		parent::start();
		parent::_init_vars();
	}
	
	static function getAll(&$PDOdb) {
	$sql = "SELECT rowid, label FROM ".MAIN_DB_PREFIX."entrepot";
		/*$PDOdb->Execute($sql);
		$Tab=array();
		while($obj = $PDOdb->Get_line()) {
			
			$Tab[$obj->code] = $obj->label;
			
		}
		
		return $Tab;
	*/
	
		return TRequeteCore::_get_id_by_sql($PDOdb, $sql, 'label','rowid');
	}
	
	/*function update() {
			
			global $db, $conf, $user;
			
			
			
			$sql= "UPDATE  ".MAIN_DB_PREFIX."alert_by_stock SET  limite =  '".$this->limite."' WHERE  fk_entrepot =".$this->fk_entrepot." AND fk_product=".$this->fk_product;
				
			$db->query($sql);
			
			
		}*/
	//Recuperation des données
	function fetch($idEntrepot, $idProduct){
		global $db, $conf, $user;
			$sql="SELECT * FROM ".MAIN_DB_PREFIX."alert_by_stock WHERE fk_entrepot = ".$idEntrepot. " AND fk_product = ".$idProduct;
			$result = $db->query($sql);
			
			$donnees = $db->fetch_object($result);
			
			$this->rowid = $donnees->rowid;
			$this->fk_entrepot = $donnees ->fk_entrepot;
			$this->fk_product = $donnees ->fk_product;
			$this->limite = $donnees->limite;
			if($donnees->limite == null){
				$this->exist = true;
			} else {
				$this->exist = false;
			}
		}
		
		//Creation/Update
		function create(){
			global $db, $conf, $user;
			
			$PDOdb = new TPDOdb;
			
			//if($this->exist){
				$this->save($PDOdb);
				/*$sql= "INSERT INTO ".MAIN_DB_PREFIX."alert_by_stock (
					`rowid` ,
					`fk_entrepot` ,
					`fk_product` ,
					`limite` 
					)
				VALUES (
					NULL ,  '".$this->fk_entrepot."',  '".$this->fk_product."',  '".$this->limite."'
				);";
				$db->query($sql);*/
			/*} else {
				$this->update();
			}*/
		}
	
} 
