<?php

class TAlertStockWarehouse extends TObjetStd {

	var $exist = false;
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
		
	
	
} 
