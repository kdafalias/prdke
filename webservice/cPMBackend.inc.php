<?php

include_once dirname(__FILE__).'/../inc/cGeneric.inc.php';

/**
 * Description of cPMBackend
 *
 * @author Dino
 */
class cPMBackend extends cGeneric
{
  private $aAktivitaeten = array();
  private $numActivities = null;
  private $numCases = null;
  private $aVarianten = array();
  
  private function get_nodes()
  {
    $sFilter = "";
    $aNodes = array();
    #$sLimit = empty($this->numActivities) ? '' : "LIMIT ".$this->numActivities;
    $sQuery = "SELECT SUM(Haeufigkeit) AS anzahl, Aktivitaet, aktivitaeten_ID FROM prozessteilschritte "
            . "INNER JOIN aktivitaeten ON prozessteilschritte.aktivitaeten_ID = aktivitaeten.ID "
            . "GROUP BY Aktivitaeten_ID "
            . "ORDER BY anzahl DESC;";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aNodes[] = array("name"=>$aRow['Aktivitaet'],"value"=>intval($aRow['anzahl']));
      $this->aAktivitaeten[$aRow['aktivitaeten_ID']] = intval($aRow['anzahl']);
    }
    $aNodes[] = array("name"=>'Start',"value"=>0);
    $aNodes[] = array("name"=>'End',"value"=>0);
    return $aNodes;
  }
  
  private function get_edges()
  {
    $sFilter = "";
    $aEdges = array();
    $sJoin = "";
    $sWhere = "WHERE 1=1";
    if(!empty($this->numActivities))
    {
      $sJoin = "INNER JOIN prozessvarianten ON prozessvarianten.ProzessvariantenID = prozessteilschritte.ProzessvariantenId";
      $sWhere .= " AND numActivities = ".$this->numActivities;
    }
    if(!empty($this->aVarianten))
    {
      $sVarianten = implode(',', $this->aVarianten);
      $sWhere .= " AND prozessteilschritte.ProzessvariantenID IN($sVarianten)";
    }
    $sQuery = "SELECT SUM(prozessteilschritte.Haeufigkeit) AS num, ak1.Aktivitaet AS target, ak2.Aktivitaet AS source,"
            . " AVG(prozessteilschritte.Durchlaufzeit) AS time, Knotentyp AS type, aktivitaeten_ID, aktivitaeten_VG_ID, "
            . " COUNT(DISTINCT ProzessteilschrittID) AS numAct"
            . " FROM prozessteilschritte "
            . "INNER JOIN aktivitaeten ak1 ON prozessteilschritte.aktivitaeten_ID = ak1.ID "
            . "LEFT JOIN aktivitaeten ak2 ON prozessteilschritte.aktivitaeten_VG_ID = ak2.ID "
            . " $sJoin "
            . " $sWhere "
            . " GROUP BY CONCAT(Aktivitaeten_ID,'_',Aktivitaeten_VG_ID);";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      if($aRow['type'] == 'start')
      {
        $aRow['source'] = "Start";
      }
      if($aRow['type'] == 'end')
      {
        $aEdges[] = array('source'=>$aRow['target'],'target'=>'End','time'=>0,'num'=>$this->aAktivitaeten[$aRow['aktivitaeten_ID']],'type'=>'end');
        $aRow['type'] = 'normal';
      }
      $aEdges[] = array("source"=>$aRow['source'], 
          "target"=>$aRow['target'],
          "time"=>intval($aRow['time']),
          "num"=>intval($aRow['num']),
          "type"=>$aRow['type']);
    }
    return $aEdges;
  }
  
  /**
   * 
   * @return array 
   */
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
  
  /**
   * Liefert alle Prozessvarianten
   * 
   * @return array
   */
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
  
  /**
   * Liefert die durchschnittliche Durchlaufzeit für alle Prozessvarianten
   * 
   * @return int
   */
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
  
  /**
   * 
   */
  public function getEventData()
  {
    $this->numActivities = !empty($_REQUEST['aktivitaeten']) ? intval($_REQUEST['aktivitaeten']) : null;
    $this->numCases = !empty($_REQUEST['cases']) ? intval($_REQUEST['cases']) : null;
    $this->aVarianten = !empty($_REQUEST['varianten']) ? $_REQUEST['varianten'] : array();
    $aNodes = $this->get_nodes();
    $aReturn = array('nodes'=>$aNodes,
        'edges'=>$this->get_edges(),
        'Dependency'=>$this->get_Dependency(),
        'Variation'=>$this->get_Variation(),
        'MeanRuntime'=>$this->get_Meantime(),
        'NumActivities'=>empty($this->numActivities) ? (count($aNodes)-2) : $this->numActivities,
        'NumCases'=>empty($this->numCases) ? (count($aNodes)-2) : $this->numCases
    );
    echo(json_encode($aReturn));
  }
}
