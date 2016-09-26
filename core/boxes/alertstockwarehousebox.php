<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
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
 * 	\file		core/boxes/mybox.php
 * 	\ingroup	alertstockwarehouse
 * 	\brief		This file is a sample box definition file
 * 				Put some comments here
 */
 
include_once DOL_DOCUMENT_ROOT . "/core/boxes/modules_boxes.php";

/**
 * Class to manage the box
 */
class alertstockwarehousebox extends ModeleBoxes
{

    public $boxcode = "alertstockwarehouse";
    public $boximg = "alertstockwarehouse@alertstockwarehouse";
    public $boxlabel;
    public $depends = array("alertstockwarehouse");
    public $db;
    public $param;
    public $info_box_head = array();
    public $info_box_contents = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        global $langs;
        $langs->load("boxes");
		
        $this->boxlabel = $langs->transnoentitiesnoconv("Seuil limite d'alerte en fonction d'un entrepot");
    }

    /**
     * Load data into info_box_contents array to show array later.
     *
     * 	@param		int		$max		Maximum number of records to load
     * 	@return		void
     */
     //Fonction qui charge la box sur la page d'accueil
    public function loadBox($max = 5)
    {
        global $conf, $user, $langs, $db;

        $this->max = $max;

        
		
        $text = $langs->trans("Produits en alerte stock en fonction de son entrepot", $max);
        $this->info_box_head = array(
            'text' => $text,
            'limit' => dol_strlen($text)
        );
		//Récuperation  des données liées au seuil dont la limite est supérieur à la quantité (produit/stock/seuil/quantité)
		$sql = '
				SELECT  p.rowid as fk_product, p.ref AS product_ref, p.label as product_label, p.fk_product_type, e.rowid AS fk_warehouse, e.label AS entrepot_label, ps.reel, abs.limite 
				FROM `'.MAIN_DB_PREFIX.'product` p 
				INNER JOIN `'.MAIN_DB_PREFIX.'alert_by_stock` abs ON (p.rowid = abs.fk_product) 
				LEFT JOIN `'.MAIN_DB_PREFIX.'product_stock` ps ON (abs.fk_product = ps.fk_product AND abs.fk_entrepot = ps.fk_entrepot) 
				INNER JOIN `'.MAIN_DB_PREFIX.'entrepot` e ON (e.rowid = abs.fk_entrepot) 
				WHERE abs.limite IS NOT NULL 
				AND (
				    	(ps.reel IS NULL AND abs.limite >= 0)
						OR 
						ps.reel <= abs.limite
					)
				';
				
				

				
				
			
		$result = $db->query($sql);
		//Affichage
		if ($result)
		{
				$num = $db->num_rows($result);
				$line = 0;
				
				$productstatic = new Product($db);
				
                while ($line < $num) {
                	
					$objp = $db->fetch_object($result);
					
					$productstatic->id = $objp->fk_product;
                    $productstatic->ref = $objp->product_ref;
                    $productstatic->type = $objp->fk_product_type;
                    $productstatic->label = $objp->product_label;
					
					$this->info_box_contents[$line][] = array(
                        'td' => 'align="left"'
                        ,'text' => $productstatic->getNomUrl(1)
                        ,'asis' => 1
                    );
                    
                    
					 $this->info_box_contents[$line][] = array(
                        'td' => 'align="left"',
                        
                        'text' => "<a href='".dol_buildpath('/product/stock/card.php?id='.$objp->fk_warehouse,1)."' >".$objp->entrepot_label."</a>",
                        'asis' => 1
                    );
                    
					
					
                  
                     $this->info_box_contents[$line][] = array('td' => 'align="center"',
                    'text' => (($objp->reel)?$objp->reel:'0') . ' / '.$objp->limite,
					'text2'=>img_warning($langs->transnoentitiesnoconv("StockLowerThanLimit")));
                    $line++;
				}
		}
		
		
    }

    /**
     * 	Method to show box
     *
     * 	@param	array	$head       Array with properties of box title
     * 	@param  array	$contents   Array with properties of box lines
     * 	@return	void
     */
    public function showBox($head = null, $contents = null)
    {
        parent::showBox($this->info_box_head, $this->info_box_contents);
    }
}