<?php

include_once dirname(__FILE__).'/../inc/cGeneric.inc.php';

/**
 * Description of cPMBackend
 *
 * @author Dino
 */
class cPMBackend extends cGeneric
{
  private function get_nodes()
  {
    $sFilter = "";
    $aNodes = array();
    $sQuery = "SELECT SUM(Haeufigkeit) AS anzahl, Aktivitaet FROM prozessteilschritte "
            . "INNER JOIN aktivitaeten ON prozessteilschritte.aktivitaeten_ID = aktivitaeten.ID "
            . "GROUP BY Aktivitaeten_ID;";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aNodes[] = array("name"=>$aRow['Aktivitaet'],"value"=>intval($aRow['anzahl']));
    }
    return $aNodes;
    
  }
  
  private function get_edges()
  {
    $sFilter = "";
    $aEdges = array();
    $sQuery = "SELECT SUM(Haeufigkeit) AS num, ak1.Aktivitaet AS source, ak2.Aktivitaet AS target,"
            . " AVG(Durchlaufzeit) AS time, Knotentyp AS type "
            . " FROM prozessteilschritte "
            . "INNER JOIN aktivitaeten ak1 ON prozessteilschritte.aktivitaeten_ID = ak1.ID "
            . "INNER JOIN aktivitaeten ak2 ON prozessteilschritte.aktivitaeten_VG_ID = ak2.ID "
            . "GROUP BY CONCAT(Aktivitaeten_ID,'_',Aktivitaeten_VG_ID);";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aEdges[] = array("source"=>$aRow['source'], 
          "target"=>$aRow['target'],
          "time"=>intval($aRow['time']),
          "num"=>intval($aRow['num']),
          "type"=>$aRow['type']);
    }
    return $aEdges;
  }
  
  private function get_Dependency()
  {
    $sFilter = "";
    $aPairs = array();
    $sQuery = "SELECT SUM(Haeufigkeit) AS num, ak1.Aktivitaet AS source, ak2.Aktivitaet AS target "
            . " FROM prozessteilschritte "
            . "INNER JOIN aktivitaeten ak1 ON prozessteilschritte.aktivitaeten_ID = ak1.ID "
            . "INNER JOIN aktivitaeten ak2 ON prozessteilschritte.aktivitaeten_VG_ID = ak2.ID "
            . "WHERE aktivitaeten_VG_ID > 0 "
            . "GROUP BY CONCAT(Aktivitaeten_ID,'_',Aktivitaeten_VG_ID);";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aPairs[] = array("FirstEventName"=>$aRow['source'], 
          "SecondEventName"=>$aRow['target'],
          "num"=>intval($aRow['num']));
    }
    return $aPairs;
  }
  
  private function get_Variation()
  {
    $sFilter = "";
    $aVarianten = array();
    $sQuery = "SELECT * FROM prozessvarianten $sFilter ORDER BY haeufigkeit DESC;";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aVarianten[] = array("VariationID"=>$aRow['ProzessvariantenID'],"num"=>intval($aRow['Haeufigkeit']));
    }
    return $aVarianten;
  }
  
  private function get_Meantime()
  {
    $sFilter = "";
    $sQuery = "SELECT AVG(Durchlaufzeit) AS average FROM prozessvarianten $sFilter;";
    $oTable = $this->oDB->query($sQuery);
    if($aRow = $oTable->fetch_assoc())
    {
      return intval($aRow['average']);
    }
    else
    {
      return 0;
    }
  }
  
  public function getEventData()
  {
    $aReturn = array('nodes'=>$this->get_nodes(),
        'edges'=>$this->get_edges(),
        'Dependency'=>$this->get_Dependency(),
        'Variation'=>$this->get_Variation(),
        'MeanRuntime'=>$this->get_Meantime()
    );
    echo(json_encode($aReturn));
  }
}
