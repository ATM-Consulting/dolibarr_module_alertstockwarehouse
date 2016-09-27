<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_alertstockwarehouse.class.php
 * \ingroup alertstockwarehouse
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsAlertStockWarehouse
 */
class ActionsAlertStockWarehouse
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the formObjectOptions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		global $db, $user, $langs;
		//Dans la vue produit, onglet stock
		if (in_array('stockproductcard', explode(':', $parameters['context'])))
		{
			
			$fk_product = $object->id;
			
			define('INC_FROM_DOLIBARR',true);
		
			dol_include_once('/alertstockwarehouse/config.php');
			dol_include_once('/alertstockwarehouse/class/stock.class.php');
			
			
			$result = '';
			//Creation/Mise à jour/Suppresion de la donnée
			if (strpos($action ,  'setlimite_') === 0)
			{
				$stocklimit = GETPOST(substr($action, 3), 'int');
				list($dummy, $fk_warehouse) = explode('_',$action);
				
   				$PDOdb = new TPDOdb;
				$stock = new TAlertStockWarehouse;
				//On récupère l'objet stock (seuil/produit/stock) 
			
				$stock->loadByWarehouseProduct($PDOdb,$fk_warehouse, $fk_product);
				
   			
   				$stock->fk_product = $fk_product;
   				$stock->fk_entrepot = $fk_warehouse;
				$stock->limite = $stocklimit;
				//Si le champ a été vidé, suppression en bdd
				if($stocklimit == NULL){
					$stock->delete($PDOdb);
				}else {
				//Sinon on enregistre en bdd
					$stock->save($PDOdb);
				}
				
				 if ($result < 0){
				    setEventMessages($object->error, $object->errors, 'errors');
				 }
				  
				$action='';
			}
			
			//Récupération des données pour l'affichage
			$sql = "SELECT e.rowid, e.label, e.entity, abs.fk_product, abs.limite";
			$sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e";
			$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'alert_by_stock as abs ON (abs.fk_entrepot = e.rowid AND abs.fk_product = '.(int) $fk_product.')';
			$sql.= " ORDER BY e.label";
			
			$resql=$db->query($sql);
			
			if ($resql && $db->num_rows($resql) > 0)
			{
				$form = new Form($db);
				//Affichage liste des seuils limite d'alerte 
				while ($obj = $db->fetch_object($resql))
				{
					print '<tr><td>'.$form->editfieldkey($langs->trans("LabelLimitList")." ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer).'</td><td colspan="2">';
				    print $form->editfieldval($langs->trans("LabelLimiteList")." ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer,'string');
				    print '</td></tr>';
				}
			}
			
		}

		
	}
}