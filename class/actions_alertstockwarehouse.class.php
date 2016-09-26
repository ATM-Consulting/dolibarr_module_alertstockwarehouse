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
		global $db, $user;
		//Dans la vue produit, onglet stock
		if (in_array('stockproductcard', explode(':', $parameters['context'])))
		{
			
			$fk_product = GETPOST('id', 'int'); //TODO AA a priori déjà présent dans $object->id, n'utiliser GETPOST dans un hook qu'en dernier recours
			
			define('INC_FROM_DOLIBARR',true);
		
			dol_include_once('/alertstockwarehouse/config.php');
			dol_include_once('/alertstockwarehouse/class/stock.class.php');
			
			
			$result = '';
			//Creation/Modification de la donnée
			if (strpos($action ,  'setlimite_') === 0)
			{
				$stocklimit = GETPOST(substr($action, 3), 'int');
				$TRes = explode('_',$action); //TODO ne serait-ce pas redondant avec $stocklimit
				// $stocklimit = $TRes[1] non ?
				
				$stock = new TStock; //TODO TStock trop generique => TAlertStockWarehouse
				
				$stock->fetch($TRes[1], $fk_product);
				
   			
   				$PDOdb = new TPDOdb;
   				$stock->fk_product = $fk_product;
   				$stock->fk_entrepot = $TRes[1];
				$stock->limite = $stocklimit;
				//
				if($stocklimit == NULL){ // TODO ===
					$stock->delete($PDOdb);
				}else {
				
					$stock->save($PDOdb);
				}
				//TODO tu peux aussi écrire $stock->to_delete = true; $stock->save($PDOdb);
				
				 if ($result < 0) //TODO { } sinon sur une seule ligne
				    setEventMessages($object->error, $object->errors, 'errors');
				  
				$action='';
			}
			
			//Récupération des données
			//TODO préfère l'écriture avec des sauts de lignes dans la chaine $sql plutôt que la concaténation
			$sql = "SELECT e.rowid, e.label, e.entity, abs.fk_product, abs.limite";
			$sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e";
			$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'alert_by_stock as abs ON (abs.fk_entrepot = e.rowid AND abs.fk_product = '.(int) $fk_product.')';
			$sql.= " ORDER BY e.label";
			
			$resql=$db->query($sql);
			
			if ($resql && $db->num_rows($resql) > 0)
			{
				$form = new Form($db);
				while ($obj = $db->fetch_object($resql)) //TODO si tu n'as pas besoin du nb de tuple, tu peux ne garder que le while
				{
					print '<tr><td>'.$form->editfieldkey("Seuil limite d'alerte pour entrepot ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer).'</td><td colspan="2">';
				    print $form->editfieldval("Seuil limite d'alerte pour entrepot ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer,'string');
				    print '</td></tr>';
				}
			}
			
		}

		
	}
}
