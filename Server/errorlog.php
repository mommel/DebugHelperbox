<?php
require 'functions.php';
$workingDir = __DIR__;
$workingDir = str_replace(DIRECTORY_SEPARATOR . 'startpage-server', '', $workingDir);

function getMonth($monthName) {
	switch (strtolower($monthName)) {
		case 'jan':
			return "01";
		case 'feb':
			return "02";
		case 'mar':
			return "03";
		case 'apr':
			return "04";
		case 'may':
		case 'mai':
			return "05";
		case 'jun':
			return "06";
		case 'jul':
			return "07";
		case 'aug':
			return "08";
		case 'sep':
			return "09";
		case 'oct':
			return "10";
		case 'nov':
			return "11";
		case 'dec':
		case 'dez':
			return "12";
		default:
			return "00";
	}
}

function statusSort($arrayGiven) {
	$newArray = array();
	foreach ($arrayGiven as $status => $text) {
		switch ($status) {
			case 'info trace':
				$id = "a";
				break;
			case 'danger':
				$id = "b";
				break;
			case 'warning':
				$id = "c";
				break;
			case 'success':
				$id = "d";
				break;
			case 'info':
				$id = "e";
				break;
			default:
				$id = "f";
		}
		$newArray[$id]['status'] = $status;
		$newArray[$id]['text'] = $text['text'];
	}
	ksort($newArray);

	return $newArray;
}

function renderHtml($linesarray) {
	krsort($linesarray);
	$output = '<ul class="list-group">
';
	foreach ($linesarray as $key => $linearray) {
		$linearray = statusSort($linearray);
		$output .= '<li class="list-group-item active"><b>' . hr($key) . '<br /></b></li>';
		$stacktraceButton = '';
		foreach ($linearray as $textline) {
			if ($textline['status'] == 'info trace') {
				$stacktraceButton = '<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse' . $key . '" aria-expanded="false" aria-controls="collapse' . $key . '">Show Stacktrace</button>';
			} else if ($textline['status'] == 'info') {
				$output .= '<li class="list-group-item list-group-item-' . $textline['status'] . '"><div id="collapse' . $key . '"  class="collapse">' . $textline['text'] . '</div></li>';
			} else {
				$output .= '<li class="list-group-item list-group-item-' . $textline['status'] . '">' . $stacktraceButton . $textline['text'] . '</li>';
			}
		}
	}

	$output .= '</li>
	</ul>
	<br /><br /><br />';

	return $output;
}

function hr($string) {
	return substr($string, 6, 2) . '.' . substr($string, 4, 2) . '.' . substr($string, 0, 4) . ' - ' . substr($string, 8, 2) . ':' . substr($string, 10, 2) . ':' . substr($string, 12, 2) . ' ';
}

function readLastLinesOfFile($filePath, $lines = 50, $filter) {
	//global $fsize;
	if (!is_file($filePath)) {
		error_out(9, false, $filePath);
	}

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
	$activeline = 0;
	$linesarray = array();

	while ($linecounter > 0) {
		$t = " ";
		$status = 'success';
		while ($t != "\n") {
			if (fseek($handle, $pos, SEEK_END) == -1) {
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

		if (preg_match('/Fatal/', $textline) > 0) {
			$status = 'danger';
		}

		if (preg_match('/Warning/', $textline) > 0) {
			$status = 'warning';
		}

		if (preg_match('/trace/', $textline) > 0) {
			$status = 'info trace';
		}

		$textpart = explode(']', $textline);
		$datepart = $textpart[0];
		$textpart[0] = preg_replace('#\[(.*) #Uis', '', $textpart[0]);
		$datepart = substr(trim(str_replace($textpart[0], '', $datepart)), 1);
		$textpart[0] = preg_replace('/[A-Za-z]/', '', $textpart[0]);
		$textpart[0] = str_replace('/', '', $textpart[0]) . '</b>';
		$textline = implode('', $textpart);

		$stacktrace = preg_match("/(PHP )[^A-Z^a-z]/", $textline);

		$search = "/\d\d(.)\d\d(.)\d\d(.){9}/";
		preg_match($search, $textline, $t2line);
		$dateparts = explode('-', $datepart);
		$activeline = $dateparts[2] . getMonth($dateparts[1]) . $dateparts[0] . str_replace(':', '', substr($t2line[0], 0, 8));

		if (!$stacktrace) {
			//$linesarray[$activeline]['type'] = $status;
			if (!isset($linesarray[$activeline]) || !isset($linesarray[$activeline][$status])) {
				$linesarray[$activeline][$status]['text'] = '';
			}
			$textline = preg_replace("/\d\d(.)\d\d(.)\d\d(.){9}/", "", $textline);
			$linesarray[$activeline][$status]['text'] .= $textline;
		} else {
			$textline = preg_replace("/\d\d(.)\d\d(.)\d\d(.){9}/", "", $textline);
			$textline = $textline . '<br />';
			if (!isset($linesarray[$activeline]) || !isset($linesarray[$activeline][$status])) {
				$linesarray[$activeline][$status]['text'] = '';
			}

			$linesarray[$activeline][$status]['text'] = $textline . $linesarray[$activeline][$status]['text'];
		}

		if ($filter['other'] && $status = 'info') {
			$text[$lines - $linecounter - 1] = $dateLine . str_replace(array("\r", "\n", "\t"), '', $textline);
		}
		if ($beginning) {
			break;
		}

	}
	fclose($handle);
	return $linesarray;
	//return array_reverse($text);
}

$filter = array('other' => false);

if (isset($_GET['other']) && $_GET['other'] == 'true') {
	$filter['other'] = true;
}

$headLine = '<b>Server:</b> ' . $_SERVER['SERVER_NAME'] . '<br />';
if ($ServerOs == '0') {
	$logfile = $workingDir . '/logs/' . LOGFILENAME;
} else {
	//$logfile = $workingDir.'/'.$_GET['proj'].'/backend/log/'.LOGFILENAME;
	$logfile = $workingDir . '/logs/' . $_GET['proj'] . '/' . LOGFILENAME;
	/*
	if(!is_file($logfile)){
	$logfile = $workingDir.'/'.$_GET['proj'].'/log/'.LOGFILENAME;
	}*/
	$headLine .= ' <b>Project:</b> ' . $_GET['proj'] . '<br />';
}
$headLine .= '<b>Processed Logfile:</b> ' . $logfile . '<br />';

$linesjson = readLastLinesOfFile($logfile, 23, $filter);

$linesjson[date("YmdHis", time())]['info headline']['text'] = $headLine;

if (isset($_REQUEST['dataType']) && strlen($_REQUEST['dataType']) > 1) {
	$dataType = $_REQUEST['dataType'];
} else {
	$dataType = 'json';
}

switch ($dataType) {
	case 'jsonPure':
		echo json_encode($linesjson);
		break;
	case 'json':
		echo json_encode(renderHtml($linesjson));
		break;
	case 'html':
		echo renderHtml($linesjson);
		break;
	default:
		echo json_encode(renderHtml($linesjson));
		break;
}
?>