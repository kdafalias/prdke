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
  private $abdeckung = null;
  private $aVarianten = array();
  private $aQuery = array();
  
  /**
   * Returns all nodes i.e. activities
   * @return array
   */
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
    // add dummy node for start and end
    $aNodes[] = array("name"=>'Start',"value"=>0);
    $aNodes[] = array("name"=>'End',"value"=>0);
    return $aNodes;
  }
  
  /**
   * Returns all edges i.e. processes
   * @return array
   */
  private function get_edges()
  {
    $sFilter = "";
    $aEdges = array();
    $sJoin = "";
    $sWhere = "WHERE 1=1";
    // Filter: Only processes with given number of activities are selected
    if(!empty($this->numActivities))
    {
      $sJoin = "INNER JOIN prozessvarianten ON prozessvarianten.ProzessvariantenID = prozessteilschritte.ProzessvariantenId";
      $sWhere .= " AND numActivities = ".$this->numActivities;
    }
    
    // Filter: Only given processes are returned
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
      // Add dummy start node
      if($aRow['type'] == 'start')
      {
        $aRow['source'] = "Start";
      }
      // Add dummy end node
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
   * Returns all process variations in context
   * 
   * @return array
   */
  private function get_Variation()
  {
    $sLimit = '';
    // Filter: Coverage (percent)
    if(!empty($this->abdeckung))
    {
      $sQueryCount = "SELECT COUNT(*) as cnt FROM prozessvarianten;";
      $oTableCount = $this->oDB->query($sQueryCount);
      if($aRow = $oTableCount->fetch_assoc())
      {
        $sLimit = 'LIMIT '.intval($aRow['cnt']/100*$this->abdeckung);
      }
    }
    $sFilter = '';
    // Filter: Process Query Language translated into Regex
    if(!empty($this->aQuery))
    {
      $sRegex = "";
      foreach($this->aQuery as $aWord)
      {
        if($aWord['id'] == '*') $aWord['id'] = '.*';
        $sRegex .= $aWord['yesNo'] ? "[^{$aWord['id']}];" : "{$aWord['id']};";
      }
      $sFilter = " WHERE CompareValue REGEXP '$sRegex'";
    }
    $aVarianten = array();
    $aVarianteID = array();
    $sQuery = "SELECT * FROM prozessvarianten $sFilter ORDER BY haeufigkeit DESC $sLimit;";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $aVarianten[] = array("VariationID"=>$aRow['ProzessvariantenID'],"num"=>intval($aRow['Haeufigkeit']));
      $aVarianteID[] = $aRow['ProzessvariantenID'];
    }
    
    $this->aVarianten = empty($this->aVarianten) ? $aVarianteID : array_intersect($this->aVarianten, $aVarianteID);
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
   * Hauptmethode, 
   */
  public function getEventData()
  {
    $this->numActivities = !empty($_REQUEST['aktivitaeten']) ? intval($_REQUEST['aktivitaeten']) : null;
    $this->abdeckung = !empty($_REQUEST['abdeckung']) ? intval($_REQUEST['abdeckung']) : null;
    $this->aVarianten = !empty($_REQUEST['varianten']) ? $_REQUEST['varianten'] : array();
    $this->aQuery = !empty($_REQUEST['query']) ? $_REQUEST['query'] : array();
    $aNodes = $this->get_nodes();
    $aReturnVarianten = $this->get_Variation();
    $aReturn = array('nodes'=>$aNodes,
        'Variation'=>$aReturnVarianten,
        'edges'=>$this->get_edges(),
        'Dependency'=>$this->get_Dependency(),
        'MeanRuntime'=>$this->get_Meantime(),
        'NumActivities'=>empty($this->numActivities) ? (count($aNodes)-2) : $this->numActivities,
        'NumVariations'=>count($aReturnVarianten)
    );
    echo(json_encode($aReturn));
  }
}
