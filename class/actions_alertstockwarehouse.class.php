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
			
			$stocklimit = GETPOST(substr($action, 3));
			define('INC_FROM_DOLIBARR',true);
		
			dol_include_once('/alertstockwarehouse/config.php');
			dol_include_once('/alertstockwarehouse/class/stock.class.php');
			
			
			$result = '';
			//Creation/Modification de la donnée
			if (strpos($action ,  'setlimite_') === 0)
			{
				
				
				
				$TRes = explode('_',$action);
				
				$stock = new TStock;
				
				$stock->fetch($TRes[1], GETPOST("id"));
								
				
				
   			//	$result=$stock->;
   				
   				$stock->fk_product = GETPOST("id");
   				$stock->fk_entrepot = $TRes[1];
				$stock->limite = $stocklimit;
				$stock->create();
				//$result=$stock->update($stock->id,$user,0,'update');
				 if ($result < 0)
				    setEventMessages($object->error, $object->errors, 'errors');
				    //else
				    //	setEventMessage($lans->trans("SavedRecordSuccessfully"));
				  $action='';
			}
			
			//Affichage des données
			$sql = "SELECT e.rowid, e.label, abs.limite, p.rowid AS idproduct";
			$sql.= " FROM ".MAIN_DB_PREFIX."entrepot as e";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."alert_by_stock as abs ON abs.fk_entrepot = e.rowid";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = abs.fk_product";
			$sql.= " AND e.entity IN (".getEntity('stock', 1).")";
			$sql.= " ORDER BY e.rowid";
			
			$resql=$db->query($sql);
			$num = $db->num_rows($resql);
			
			
			
			
			
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);

				$form = new Form($db);
									
				print '<tr><td>'.$form->editfieldkey("Seuil limite d'alerte pour entrepot ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer).'</td><td colspan="2">';
			    print $form->editfieldval("Seuil limite d'alerte pour entrepot ".$obj->label,'limite_'.$obj->rowid.'',$obj->limite,$object,$user->rights->produit->creer,'string');
			    print '</td></tr>';
				
				$i++;
			}
			
		}

		
	}
}