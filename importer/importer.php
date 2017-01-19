<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once dirname(__FILE__).'/cExcelImport.inc.php';
include_once dirname(__FILE__).'/cExtractProcess.inc.php';

$oExcelImport = new cExcelImport();
$oExcelImport->importData();
$oExcelImport->findCases();

$oExtractProcess = new cExtractProcess();
$oExtractProcess->main();

