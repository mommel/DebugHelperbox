<?php 
require 'functions.php';
ob_start();
?>
<div class="btn-group">
<?php
$links = array();
$linksFront = array();
$linksBeesiteQueue = array();
$linksBC = array();
$names = array();
$workingDir = __DIR__;
$workingDir = str_replace('/startpage-server', '', $workingDir);
$handle=opendir ($workingDir);
$servername = $_SERVER['SERVER_NAME'];
$server = str_replace('.','',$servername);
$server = str_replace('-','',$server);
$btnpre = 'btn'.$server;
$url = $_SERVER['SERVER_NAME'];
$i=0;
$count = 0;
$filearray = array();
while ($datei = readdir ($handle)) {
	$filearray[]= $datei;
}
asort($filearray);
foreach($filearray as $datei) {
	$isHR = false;
	preg_match("/(hr)/", $datei, $output_array);
	if(count($output_array)>0) $isHR = true;			
	if(is_dir($workingDir.DIRECTORY_SEPARATOR.$datei) && substr($datei,0,1)!="." && !in_array($datei, $noProjects) && $isHR){
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
		if(is_dir($workingDir.DIRECTORY_SEPARATOR.$datei.'/www/')){
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/www/';
			$type[$btnpre.$datei] = 1;
		}
		elseif(is_dir($workingDir.DIRECTORY_SEPARATOR.$datei.'/wms/')){
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/wms/';
			$type[$btnpre.$datei] = 2;
		}else{
			$links[$btnpre.$datei] = 'http://'.$url.'/'.$datei;
			$type[$btnpre.$datei] = 0;
		}	

		if(is_dir($workingDir.DIRECTORY_SEPARATOR.$datei.'/frontend/www/')){
			$linksFront[$btnpre.$datei] = 'http://'.$url.'/'.$datei.'/frontend/www';
		}else{
			$linksFront[$btnpre.$datei] = 'http://'.$url.'/'.$datei;	
		}	
		$i++;
	}		
}
if($count <1){
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
var types<?=$server?> = new Array();
<?php
foreach ($type as $project => $name){
?>
	types<?=$server?>['<?=$project?>'] ='<?=$name?>';
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
var <?=$server?>PagesBQ = new Array();
var <?=$server?>linksBC = new Array();
<?php
foreach ($linksBC as $project => $link){
	?>
	<?=$server?>linksBC['<?=$project?>'] ='<?=$link?>';
	<?	
}
?>
var <?=$server?>frontendLink = '';
var <?=$server?>backendLink = '';
var <?=$server?>bQLink = '';
var <?=$server?>harBtn = '';

$(".<?=$server?>Button").click(function (){	
	clearTimeout(errortimer);
	<?=$server?>frontendLink = <?=$server?>PagesFrontend[this.id];
	<?=$server?>backendLink = <?=$server?>Pages[this.id];
	<?=$server?>bQLink = <?=$server?>PagesBQ[this.id];
	<?=$server?>linkBC = <?=$server?>linksBC[this.id];
	activeproject = projects<?=$server?>[this.id];
	selectedProject = projects<?=$server?>[this.id];
	selectedServer = '<?=$servername?>';
	selectedTypes = types<?=$server?>[this.id];
	<?=$server?>harBtn = 'http://<?=$url?>/startpage-server/replacehtaccess.php?proj=' + selectedProject + '&type=' + selectedTypes;
	<?=$server?>celBtn = 'http://<?=$url?>/startpage-server/replacehtaccess.php?rebuild=x&proj=' + selectedProject + '&type=' + selectedTypes;
	startRefresh();
});

$("#<?=$server?>frontendBtn").click(function (){	
	window.open(<?=$server?>frontendLink);
});

$("#<?=$server?>backendBtn").click(function (){	
	window.open(<?=$server?>backendLink);
});

$("#<?=$server?>bQBtn").click(function (){	
	window.open(<?=$server?>bQLink);
});
$("#<?=$server?>harBtn").click(function (){	
	$.ajax({
        type: "POST",
        url: <?=$server?>harBtn,
        data: {}
    }).done(function(msg) {
    	var obj = jQuery.parseJSON(msg);
    	if(obj.trim() == 'OK'){    	
			$("#harok").removeClass('hide');
			alerttimerharok = setTimeout(harOkHide, 1500);
    	}else{
    		$("#harerrormsg").html(obj);
    		$("#harerror").removeClass('hide');
    		alerttimerharerror = setTimeout(harErrorHide, 8000);
    	}
	});
});

$("#<?=$server?>celBtn").click(function (){	
	$.ajax({
        type: "POST",
        url: <?=$server?>celBtn,
        data: {}
    }).done(function(msg) {
    	var obj = jQuery.parseJSON(msg);
	});
});

$('#<?=$server?>btnCronXmlSandbox').click(function(event) {
    if (selectedProject != '') {
    	$("#genxmlstartmsg").html(selectedProject);
    	$("#genxmlstart").removeClass('hide');
		alerttimergenxml = setTimeout(genXmlHide, 4500);

        $.ajax({
            type: "POST",
            url: "http://<?=$url?>/" + selectedProject + "/backend/cron/cron_update_jobad_html_active.php",
            data: {},
        });
        $.ajax({
            type: "POST",
            url: "http://<?=$url?>/" + selectedProject + "/backend/cron/cron_update_jobad_html.php",
            data: {},
        });
    }
});

$('#<?=$server?>btnBc').click(function(event) {
	window.open(<?=$server?>linkBC);    
});
</script>
<h2>&nbsp;</h2>
<div class="btn-group">
  	<button type="button" id="<?=$server?>frontendBtn" class="staticButton btn btn-primary">Open Frontend</button>
	<button type="button" id="<?=$server?>backendBtn" class="staticButton btn btn-warning">Open Backend</button>
	<? if($ServerOs == '1'){?>
	<button type="button" id="<?=$server?>harBtn" class="staticButton btn btn-primary">Replace htaccess</button>
	<button type="button" id="<?=$server?>celBtn" class="staticButton btn btn-warning">Clear errorlog</button>
	<?}?>
	<button type="button" id="<?=$server?>btnCronXmlSandbox" class="btn btn-info">Generate Jobad XML &amp; HTML in Sandbox</button>
	<br />
</div>
<br />
<?
$output = ob_get_contents();
ob_clean();
$dataType  = $_REQUEST['dataType'];
switch ($dataType) {
	case 'json':
			echo json_encode($output);
		break;
	case 'html':
			echo json_encode($output);
		break;	
	default:
			echo json_encode($html);
		break;
}

