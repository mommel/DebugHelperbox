<?php
require 'functions.php';
$workingDir = __DIR__;
$workingDir = str_replace(DIRECTORY_SEPARATOR . 'startpage-server', '', $workingDir);

function doReplace($filename, $logfiledir, $logfile, $projectname, $isFrontend) {
	if (!is_file($filename)) {
		error_out(2, $isFrontend);
	}

	if (!is_writable($filename)) {
		error_out(3, $isFrontend);
	}

	if (!is_dir($logfiledir)) {
		if (!mkdir($logfiledir)) {
			error_out(5, $isFrontend);
		} elseif (!touch($logfile)) {
			error_out(6, $isFrontend);
		} else {
			$filehandlerNewhtaccess = @fopen($logfile, 'w') or error_out(8, $isFrontend);
			fwrite($filehandlerNewhtaccess, '[01-Jan-1970 00:00:01] PHP Notice: New Logfile created
');
			fclose($filehandlerNewhtaccess);
		}
	} else {
		if ($_GET['rebuild'] == 'x') {
			$filehandlerNewhtaccess = @fopen($logfile, 'w') or error_out(8, $isFrontend);
			fwrite($filehandlerNewhtaccess, '[01-Jan-1970 00:00:01] PHP Notice: New Logfile created
');
			fclose($filehandlerNewhtaccess);
		}
	}
	$filecontent = file($filename);
	$filehandler = @fopen($filename, 'w') or error_out(4, $isFrontend);
	foreach ($filecontent as $line) {
		if (substr(trim($line), 0, 19) == 'php_value error_log') {
			$line = 'php_value error_log ' . $logfile;
		}
		fwrite($filehandler, $line);
	}
	fclose($filehandler);
}

$projectname = $_GET['proj'];
$type = $_GET['type'];
//$logfiledir = $workingDir . '/'. $projectname . '/log';
$logfiledir = $workingDir . '/logs/' . $projectname;
$logfile = $logfiledir . '/' . LOGFILENAME;

if (!isset($projectname) || strlen($projectname) <= 0 || strtolower($projectname) == 'nothing') {
	error_out(1);
}

switch ($type) {
	case '1':
		$filename = $workingDir . '/' . $projectname . '/.htaccess';
		doReplace($filename, $logfiledir, $logfile, $projectname, false);
		break;

	case '2':
		$filename = $workingDir . '/' . $projectname . '/backend/.htaccess';
		doReplace($filename, $logfiledir, $logfile, $projectname, false);

		$filenameFrontend = $workingDir . '/' . $projectname . '/frontend/www/.htaccess';
		if (!is_file($filenameFrontend)) {
			$filenameFrontend = $workingDir . '/' . $projectname . '/frontend/.htaccess';
		}
		doReplace($filenameFrontend, $logfiledir, $logfile, $projectname, true);
		break;

	default:
		error_out(7);
		break;
}
echo json_encode('OK');