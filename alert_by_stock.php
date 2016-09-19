<?php
	
	
	require 'config.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	
	dol_include_once('/alertstockwarehouse/class/stock.class.php');
	dol_include_once('/core/lib/files.lib.php');
	global $conf,$langs,$user;
	$action = GETPOST('action');
	
	$PDOdb=new TPDOdb;
	$langs->load("product");
	$langs->load("other");

	
	$id=GETPOST('id');
	$ref=GETPOST('ref');
	//dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');
	$stock = new TStock;
	
	
	
	
	llxHeader();
	//dol_fiche_head();
	
	$object = new Product($db);
	
	
	$object->fetch($id,$ref);
	$head=product_prepare_head($object);
	
    $titre=$langs->trans("CardProduct".$object->type);
    $picto=($object->type== Product::TYPE_SERVICE?'service':'product');
    dol_fiche_head($head, 'OngletAlertByStock', $titre, 0, $picto);




	
	$linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php">'.$langs->trans("BackToList").'</a>';
	$parameters=array('id'=>$id);
	dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');
	
	
	
	
	
	
	
	switch ($action) {
		case 'add':
			_card($stock, 'edit');
			break;
		case 'edit':
		
			$stock->load($PDOdb, GETPOST('idStock'));
			_card($stock,'edit');
			break;
		case 'delete':
			
			$stock->load($PDOdb, GETPOST('idStock'));
			$langs->load("companies");	// Need for string DeleteFilse+ConfirmDeleteFiles
			_card($stock,'view',1);
			break; 
		case 'save':
			$stock->load($PDOdb, GETPOST('idStock'));
			
			$stock->set_values($_POST);
			$stock->id=GETPOST('idStock');
			$stock->save($PDOdb);
			_card($stock, 'view');
			break;
		case 'view':
			$stock->load($PDOdb, GETPOST('idStock'));
			_card($stock,'view');
			break;
		case 'confirm_deletefile':
			if(GETPOST('confirm')=="yes"){
				$stock->load($PDOdb, GETPOST('idStock'));
				$stock->delete($PDOdb);	
				_list();
			}else{
				_card($stock, 'view');
			}
			break;
		default :
			_list();
	}
	
	
	function _list() {
		
		
		$PDOdb=new TPDOdb;
		$idproduct= GETPOST("id");
		
		
		$sql="SELECT rowid, fk_entrepot, limite, '' AS action
		FROM ".MAIN_DB_PREFIX."alert_by_stock
		WHERE fk_product = ".$idproduct."
		";
		
		$formCore = new TFormCore('auto','formEntrepot','post');
		$l = new TListviewTBS('listEntrepot');
		$formCore->Set_typeaff("edit");
		echo $l->render($PDOdb, $sql,array(
			'title'=>array(
				'rowid'=>'Id'
				,'fk_entrepot'=>'Entrepot'
				,'limite'=>'Limite'
			)
			,'translate'=>array(
				'fk_entrepot'=>TStock::getAll($PDOdb)
			)
			,'link'=>array(
				'action'=>"<a href='?action=view&id=".$idproduct."&idStock=@rowid@' >".img_picto("edit", "edit")."</a> <a href='?action=delete&id=".$idproduct."&idStock=@rowid@' >".img_picto("delete", "delete")."</a>"
			)
			,
		
		));
	
		$formCore->end();
			
		echo '<div class="tabsAction">';
	
		echo '<a class="butAction" href="?action=add&id='.$idproduct.'">Ajouter</a>';
		echo '</div>';
	}


	function _card(&$stock, $mode, $show_confirm=0){
		
		global $conf, $langs, $user;
	
	
	$idproduct=GETPOST("id");
	if ($show_confirm)
	{
		$form = new Form($db);
		$ret = $form->form_confirm(
				$_SERVER["PHP_SELF"] . '?id='.$idproduct.'&idStock=' . GETPOST("idStock") . '&urlfile=' . urlencode(GETPOST("urlfile")) . '&linkid=' . GETPOST('linkid', 'int') . (empty($param)?'':$param),
				$langs->trans('DeleteFile'),
				$langs->trans('ConfirmDeleteFile'),
				'confirm_deletefile',
				'',
				0,
				1
		);
		
		if ($ret == 'html') print '<br>';
	}
	
	
	$formCore = new TFormCore('auto','formStock','post', true);

	$formCore->Set_typeaff($mode);
	
	echo $formCore->hidden('action', 'save');
	echo $formCore->hidden('idStock', GETPOST("idStock"));
	echo $formCore->hidden('id', $idproduct);
	echo "Limite : ";
	echo $formCore->texte('','limite', $stock->limite, 80,255);
	echo '<hr />';
	echo $formCore->hidden('fk_product', $idproduct);
	$PDOdb=new TPDOdb;
	echo "Entrepot : ";
	echo $formCore->combo('','fk_entrepot', TStock::getAll($PDOdb), $stock->fk_entrepot);
	$stock->id = GETPOST("idStock");
	echo '<hr />';
	
	
	if($mode == 'edit') {
		echo $formCore->btsubmit($langs->trans('Save'), 'bt_save');	
	}
	else{
			
	
		echo $formCore->bt($langs->trans('Edit'), 'bt_edit', ' onclick="document.location.href = \'?action=edit&idStock='.$stock->getId().'&id='.$idproduct.'\'" ');	
	}
	$formCore->end();
	

	
	
	
		
	}
	
	
	
	
	//$object->info($object->id);
	
	//dol_fiche_end();
	llxFooter();