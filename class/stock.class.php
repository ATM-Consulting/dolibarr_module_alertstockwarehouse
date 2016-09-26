<?php

class TStock extends TObjetStd { // TODO renommer class en TAlerteStockWarehouse

	var $exist = false;

	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'alert_by_stock');
		parent::add_champs('code', array('index'=>true) ); // Attention, tu index une varchar(255) avec ça, préfère une chaine plus courte avec le paramètre length
		parent::add_champs('fk_product', array('type'=>'integer', 'index'=>true));
		parent::add_champs('fk_entrepot', array('type'=>'integer', 'index'=>true));
		parent::add_champs('limite', array('type'=>'integer'));
		
		parent::start();
		parent::_init_vars();
	}
	
	static function getAll(&$PDOdb) {
		$sql = "SELECT rowid, label FROM ".MAIN_DB_PREFIX."entrepot";
		
		return TRequeteCore::_get_id_by_sql($PDOdb, $sql, 'label','rowid');
	}
	
	
	//Recuperation des données
	function fetch($idEntrepot, $idProduct){
		
		//TODO c'est quoi cette fonction.
		// normalement elle devrait s'appeler loadByWarehourProduct(&$PDOdb, $fk_wharehouse, $fk_product)
		// récupérer le rowid de la ligne et lancer le $this->load($PDOdb, $id);
		
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
