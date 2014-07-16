<?php 
	header('Access-Control-Allow-Origin: *'); 
	error_reporting(E_ALL); 
	ini_set("display_errors", 1); 
?>
<div class="btn-group">
<?php
$links = array();
$linksFront = array();
$names = array();
$handle=opendir (__DIR__);
$servername = $_SERVER['SERVER_NAME'];
$server = str_replace('.','',$servername);
$server = str_replace('-','',$server);
$btnpre = 'btn'.$server;
$url = $_SERVER['SERVER_NAME'];
$i=0;
$count = 0;
while ($datei = readdir ($handle)) {
	if(is_dir(__DIR__.DIRECTORY_SEPARATOR.$datei) && substr($datei,0,1)!="." && $datei!="logs" && $datei!="hide"){
		$count++;
		?>
		<button type="button" id="<?=$btnpre?><?=$datei?>" class="<?=$server?>Button Primary btn btn-<? 
		if($i%2){
			echo "primary";
		}
		else{
			echo "info";
		}
		?>"><?=$datei?></button>
		<?
		$names[$btnpre.$datei] = $datei;
		if(is_dir(__DIR__.DIRECTORY_SEPARATOR.$datei.'/www/admin')){
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/www/admin';			
		}
		elseif(is_dir(__DIR__.DIRECTORY_SEPARATOR.$datei.'/backend/www/admin')){
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/backend/www/admin';
		}else{
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei;	
		}	

		if(is_dir(__DIR__.DIRECTORY_SEPARATOR.$datei.'/frontend/www/')){
			$linksFront[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/frontend/www';
		}else{
			$linksFront[$btnpre.$datei] = 'http://'.$url.'/'.$datei;	
		}	
		$i++;
	}		
}
if($count <=1){
	?>
	<button type="button" class="Primary btn btn-warning">Keine Projekte vorhanden</button>
	<?php
}
closedir($handle);
?>
</div>
<script type="text/javascript">
var projects<?=$server?> = new Array();
<?php
foreach ($names as $project => $name){
?>
	projects<?=$server?>['<?=$project?>'] ='<?=$name?>';
<?	
}
?>
var <?=$server?>Pages = new Array();
<?php
foreach ($links as $project => $link){
?>
	<?=$server?>Pages['<?=$project?>'] ='<?=$link?>';
<?	
}
?>
var <?=$server?>PagesFrontend = new Array();
<?php
foreach ($linksFront as $project => $link){
?>
	<?=$server?>PagesFrontend['<?=$project?>'] ='<?=$link?>';
<?	
}
?>
var <?=$server?>frontendLink = '';
var <?=$server?>backendLink = '';

$(".<?=$server?>Button").click(function (){	
	clearTimeout(errortimer);
	<?=$server?>frontendLink = <?=$server?>PagesFrontend[this.id];
	<?=$server?>backendLink = <?=$server?>Pages[this.id];
	selectedProject = projects<?=$server?>[this.id];
	startRefresh();
});

$("#<?=$server?>frontendBtn").click(function (){	
	window.open(<?=$server?>frontendLink);
});

$("#<?=$server?>backendBtn").click(function (){	
	window.open(<?=$server?>backendLink);
});
</script>
<h2>&nbsp;</h2>
<div class="btn-group">
  	<button type="button" id="<?=$server?>frontendBtn" class="staticButton btn btn-primary">Open Frontend</button>
	<button type="button" id="<?=$server?>backendBtn" class="staticButton btn btn-warning">Open Backend</button>  		
</div>