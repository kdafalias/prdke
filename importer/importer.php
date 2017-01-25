<?php

/* 
 * After Excel file is placed in files directory, call this in Browser to import data
 */

include_once dirname(__FILE__).'/cExcelImport.inc.php';
include_once dirname(__FILE__).'/cExtractProcess.inc.php';

$oExcelImport = new cExcelImport();
$oExcelImport->importData();
$oExcelImport->findCases();

$oExtractProcess = new cExtractProcess();
$oExtractProcess->main();

