<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once( dirname(__FILE__). '/config.inc.php');

class cGeneric
{
  protected $oDB;
  
  protected function connect_db()
  {
    global $aDB;
    $this->oDB = new mysqli($aDB['server'], $aDB['user'], $aDB['password'], $aDB['db']);
    if($this->oDB->connect_errno) {
      echo("Datenbank-Verbindungsfehler<br>");
    }
    $this->oDB->set_charset("utf8");
    $this->oDB->init();
    
  }
  
  public function __construct() {
    $this->connect_db();
  }
}