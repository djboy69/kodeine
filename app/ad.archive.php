<?php
	require(dirname(dirname(__FILE__)).'/api/core.admin.php');
	$app = new coreAdmin();

	if(!$app->userIsAdmin) header("Location: ./");

	// Remove
	if(sizeof($_POST['del']) > 0){
		foreach($_POST['del'] as $e){
			$app->apiLoad('ad')->adRemove($e);
		}
		header("Location: ad.archive.php");
	}

	// Filter
	if(isset($_GET['cf'])){
		$app->filterSet('ad-archive', $_GET);
		$filter = array_merge($app->filterGet('ad'), $_GET);	
	}else
	if(isset($_POST['filter'])){
		$app->filterSet('ad-archive', $_POST['filter']);
		$filter = array_merge($app->filterGet('ad-archive'), $_POST['filter']);	
	}else{
		$filter = $app->filterGet('ad-archive');
	}

	$ad = $app->apiLoad('ad')->adGet(array(
		'search'	=> $filter['q'],
		'withZone'	=> true,
		'debug'		=> false,
		'is_active'	=> false,
		'order'		=> $filter['order'],
		'direction'	=> $filter['direction'],
		'limit'		=> $filter['limit'],
		'offset'	=> $filter['offset']
	));

	$total	= $app->apiLoad('ad')->total;
	$limit	= $app->apiLoad('ad')->limit;
	$dir	= ($filter['direction'] == 'ASC') ? 'DESC' : 'ASC';

	include(ADMINUI.'/doctype.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Kodeine</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<?php include(ADMINUI.'/head.php'); ?>
</head>
<body>
<div id="pathway">
	<a href="core.panel.php">Admin</a> &raquo;
	<a href="ad.index.php">Publicité</a> &raquo;
	<a href="ad.archive.php">Archive</a>
	<?php include(ADMINUI.'/pathway.php'); ?>
</div>

<?php include('ressource/ui/menu.ad.php'); ?>

<div class="app">

<div class="quickForm clearfix">
	<div class="upper clearfix">
		<div class="label"><a href="javascript:filterToggle('ad-archive');">OPTIONS</a></div>
	</div>
	<form action="ad.archive.php" method="post" id="filter" style="<?php echo ($filter['open']) ? '' : 'display:none;' ?>">
		<input type="hidden" name="optForm"			value="1" />
		<input type="hidden" name="filter[open]"	value="1" />
		<input type="hidden" name="filter[offset]"	value="0" />

		Recherche
		<input type="text" name="filter[q]" value="<?php echo $filter['q'] ?>" />

		Combien
		<input type="text" name="filter[limit]" value="<?php echo $filter['limit'] ?>" size="3" />

		<input type="submit" />
	</form>
</div>

<form method="post" action="ad.archive.php" id="listing">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="listing">
	<thead>
		<tr>
			<th width="30"  class="icone"><img src="ressource/img/ico-delete-th.png" height="20" width="20" /></th>
			<th	width="50"  class="order <?php if($filter['order'] == 'id_ad')		echo 'order'.$dir; ?>"  onClick="document.location='ad.archive.php?cf&order=id_ad&direction=<?php echo $dir ?>'"><span>#</span></th>
			<th				class="order <?php if($filter['order'] == 'adName')	echo 'order'.$dir; ?>"  onClick="document.location='ad.archive.php?cf&order=adName&direction=<?php echo $dir ?>'"><span>Nom</span></th>
			<th width="200" class="order <?php if($filter['order'] == 'zoneName')	echo 'order'.$dir; ?>"  onClick="document.location='ad.archive.php?cf&order=zoneName&direction=<?php echo $dir ?>'"><span>Zone</span></th>
			<th width="100" class="order <?php if($filter['order'] == 'adView') 	echo 'order'.$dir; ?>"  onClick="document.location='ad.archive.php?cf&order=adView&direction=<?php echo $dir ?>'"><span>Vue</span></th>
			<th width="100" class="order <?php if($filter['order'] == 'adClick')	echo 'order'.$dir; ?>"  onClick="document.location='ad.archive.php?cf&order=adClick&direction=<?php echo $dir ?>'"><span>Click</span></th>
		</tr>
	</thead>
	<?php if(sizeof($ad) > 0){ foreach($ad as $e){ ?>
		<tr>
			<td><input type="checkbox" name="del[]" value="<?php echo $e['id_ad'] ?>" class="cb" <?php echo $disabled ?> /></td>
			<td><a href="ad.data.php?id_ad=<?php echo $e['id_ad'] ?>"><?php echo $e['id_ad'] ?></a></td>
			<td><a href="ad.data.php?id_ad=<?php echo $e['id_ad'] ?>"><?php echo $e['adName'] ?></a></td>
			<td><?php echo $e['zoneName'] ?></td>
			<td><?php echo number_format($e['adView'],  0, '.', ' '); ?></td>
			<td><?php echo number_format($e['adClick'], 0, '.', ' '); ?></td>
		</tr>
	<?php } }else{ ?>
	<tr>
		<td colspan="6" style="text-align:center; padding:50px 0px 50px 0px; font-weight:bold;">Il n'y aucune publicité avec ces critères de recherche.<br /><br /><a href="ad.data.php">Ajouter une piblicité maintenant</a></td>
	</tr>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td height="25"><input type="checkbox" onchange="$$('.cb').set('checked', this.checked);" /></td>
			<td colspan="4">
				<a href="#" onClick="remove();" class="button rButton">Supprimer la selection</a> 
				<span class="pagination"><?php $app->pagination($app->total, $app->limit, $filter['offset'], 'ad.archive.php?cf&offset=%s'); ?></span>
			</td>
			<td class="pagination"><?php $app->pagination($total, $limit, $filter['offset'], 'ad.archive.php?cf&offset=%s'); ?></td>
		</tr>
	</tfoot>
</table>
</form>

<script>
	function remove(){
		if(confirm("SUPPRIMER ?")){
			$('listing').submit();
		}
	}
</script>

</div></body></html>