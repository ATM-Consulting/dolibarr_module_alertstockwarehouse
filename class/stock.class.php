<?php

class TAlertStockWarehouse extends TObjetStd {

	
	//Création de la bdd
	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'alert_by_stock');
		
		parent::add_champs('fk_product', array('type'=>'integer', 'index'=>true));
		parent::add_champs('fk_entrepot', array('type'=>'integer', 'index'=>true));
		parent::add_champs('limite', array('type'=>'integer'));
		
		parent::start();
		parent::_init_vars();
	}
	//Récupération des données liées à l'entrepot
	static function getAll(&$PDOdb) {
		$sql = "SELECT rowid, label FROM ".MAIN_DB_PREFIX."entrepot";

		return TRequeteCore::_get_id_by_sql($PDOdb, $sql, 'label','rowid');
	}
	
	
	//Recuperation des données en fonction de l'entrepot lié à ce produit
	function loadByWarehouseProduct(&$PDOdb, $fk_warehouse, $fk_product){
			
		
		global $db, $conf, $user;
			$sql="SELECT rowid FROM ".MAIN_DB_PREFIX."alert_by_stock WHERE fk_entrepot = ".$fk_warehouse. " AND fk_product = ".$fk_product;
			$result = $db->query($sql);
			$donnees = $db->fetch_object($result);
			$this->load($PDOdb, $donnees->rowid);
		
	}

} 
