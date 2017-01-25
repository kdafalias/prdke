<?php

/* 
 * Calls stored procedures to extract data from eventlog
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