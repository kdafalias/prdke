<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once dirname(__FILE__).'/cExcelImport.inc.php';

$oExcelImport = new cExcelImport();
$oExcelImport->importData();
$oExcelImport->findCases();
$oExcelImport->fillEventlog();

