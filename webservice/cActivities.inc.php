<?php
include_once dirname(__FILE__).'/../inc/cGeneric.inc.php';

/**
 * All activities for dropdown field
 *
 * @author Dino
 */
class cActivities extends cGeneric
{
  public function main()
  {
    $sQuery = "SELECT ID, Aktivitaet FROM aktivitaeten ORDER BY ID;";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      echo("<option value=\"{$aRow['ID']}\">{$aRow['Aktivitaet']}</option>\n");
    }
  }
}