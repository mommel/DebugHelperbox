<?php
    header('Access-Control-Allow-Origin: *'); 
    error_reporting(E_ALL); 
    ini_set("display_errors", 1); 
?>
<ul class="list-group">
<?
$filter = array('other'=> false);

if(isset($_GET['other']) && $_GET['other'] == 'true'){
    $filter['other'] = true;
}  
?>
<li class="list-group-item list-group-item-info">
<?=date("d.m.Y h:i:s",time())?><br />
<?
$lines = readLastLinesOfFile(__DIR__.'/logs/PHP_errors.log', 23,$filter) ;
foreach ($lines as $line) {
    echo $line;
}  
 
function readLastLinesOfFile($filePath, $lines = 10,$filter) {
    //global $fsize;
    $handle = fopen($filePath, "r");
    if (!$handle) {
        return array();
    }
    $stacktrace = false;
    $linecounter = $lines;
    $pos = -2;
    $lastDate = '01-Jan-1970';
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        $status = 'success';
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter--;
        if ($beginning) {
            rewind($handle);
        }

        $textline = fgets($handle);

        $status = 'info';
        $dateLine = '';

        if(preg_match('/Fatal/', $textline)>0){
            $status = 'danger';
        }

        if(preg_match('/Warning/', $textline)>0){
            $status = 'warning';
        }

        if(preg_match('/trace/', $textline)>0){
            $status = 'success';
        }


        $textpart = explode(']',$textline);
        $textpart[0] = preg_replace('#\[(.*) #Uis', '', $textpart[0]);
        $textpart[0] = preg_replace('/[A-Za-z]/', '', $textpart[0]);
        $textpart[0] = str_replace('/', '', $textpart[0]).'</b>';
        $textline = implode('', $textpart);        
        
        $stacktrace = preg_match("/(PHP )[^A-Z^a-z]/", $textline);

        if(!$stacktrace){
            $textline = '</li><li class="list-group-item list-group-item-'.$status.'"><b>'.$textline.'<br />';    
        }else{
            $textline = preg_replace("/\d\d(.)\d\d(.)\d\d(.){9}/", "</b>", $textline);
            $textline = '<b>'.$textline.'</b><br />'; 
        }
        

        if($filter['other'] && $status = 'info'){
            $text[$lines-$linecounter-1] = $dateLine.str_replace(array("\r", "\n", "\t"), '', $textline);    
        }        
        if ($beginning) break;
    }
    fclose($handle);
    return array_reverse($text);
}
?>
</li>
</ul>
<br /><br /><br />