<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once dirname(__FILE__).'/../inc/cGeneric.inc.php';

class cExtractProcess extends cGeneric
{
  public function main()
  {
    $sQuery = "CALL extract_events";
    $this->oDB->query($sQuery);
  }
}