<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once dirname(__FILE__).'/../lib/PHPExcel/Classes/PHPExcel/IOFactory.php';
include_once dirname(__FILE__).'/../inc/cGeneric.inc.php';

class cExcelImport extends cGeneric
{
    private $aSheets = array(
        array('rowstart'=>4,'cols'=>2, 'tablename'=>'Aktivitaeten', 'mandatory'=>array('ID','Bezeichnung'), 'fields'=>array('ID', 'Aktivitaet')),
        array('rowstart'=>2,'cols'=>10, 'tablename'=>'Kreditor', 'mandatory'=>array('KredNr','ErstellUDS','ErstellTS'), 'fields'=>array('KredNr', 'Vname', 'Nname', 'Firma', 'PLZ', 'Ort', 'Land', 'SperrKZ', 'ErstellUSR', 'ErstellTS')),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Aenderungshistorie', 'mandatory'=>array('Tabelle','Feld', 'ID', 'AenderTS'), 'fields'=>array('AenderNr', 'Tabelle', 'Feld', 'ID', 'Wert_alt', 'Wert_neu', 'AenderTS', 'AenderUSR')),
        array('rowstart'=>2,'cols'=>7, 'tablename'=>'Bestellung', 'mandatory'=>array('BestellNr'), 'fields'=>array('BestellNr', 'KredNr', 'StornoKZ', 'ErstellTS', 'ErstellUSR', 'FreigabeTS', 'FreigabeUSR')),
        array('rowstart'=>2,'cols'=>10, 'tablename'=>'Bestellposition', 'mandatory'=>array('BestellNr','PosNr', 'ErstellTS'), 'fields'=>array('PosNr', 'BestellNr', 'MaterialNr', 'Menge', 'Meinheit', 'Preis', 'Waehrung', 'StornoKZ', 'ErstellTS', 'ErstellUSR')),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Wareneingang', 'mandatory'=>array('BestellNr','EingangsTS'), 'fields'=>array('ID', 'PosNr', 'BestellNr', 'Menge', 'Meinheit', 'EingangTS', 'EingangUSR', 'Kreditor_KredNr')),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Rechnung', 'mandatory'=>array('BestellNr','EingangsDat'), 'fields'=>array('RechNr', 'PosNr', 'BestellNr', 'EingangsDat', 'RechnungsDatum', 'Betrag', 'Waehrung', 'KredNr')),
        array('rowstart'=>2,'cols'=>7, 'tablename'=>'Zahlung', 'mandatory'=>array('RechnNr','ZahlTS'), 'fields'=>array('ID', 'RechNr', 'Betrag', 'Waehrung', 'ZahlTS', 'ZahlUSR', 'KredNr'))
    );

  
  
  /**
   * Rekursive Funktion
   * 
   * @param array $aTable Unterzweig des Table-Baums
   * @param array $aID IDs des übergeordneten Blatts 
   * @param integer $iParentCaseID Case-ID der Wurzel des Teilbaums
   */
  private function findCaseTable($aTable, $aID = array(), $iParentCaseID = null)
  {
    try {
      $sIdName = implode(',',$aTable['idname']);
      
    } catch (Exception $exc) {
      echo $exc->getTraceAsString();
    } 

    $sQueryAct = "SELECT * FROM aktivitaeten WHERE tabelle = '{$aTable['table']}'";
    $aActivities = array();
    $oTableAct = $this->oDB->query($sQueryAct);
    while($aRowAct = $oTableAct->fetch_assoc())
    {
      $aActivities[] = $aRowAct;
    }
    $sQuery = "SELECT $sIdName FROM {$aTable['table']} WHERE 1=1 ";    
    foreach($aTable['fkname'] as $sIndex)
    {
      $sQuery .= " AND $sIndex = ".$aID[$sIndex];
    }

//    echo($sQuery." kweri<br>");
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      if(empty($iParentCaseID)) 
      {
        $this->oDB->query("INSERT INTO Cases VALUES()");
        $iCaseID = $this->oDB->insert_id;
      }
      else
      {
        $iCaseID = $iParentCaseID;
      }
      $sWhere = '';
      $sID = '';
      $iPrimID = null;
      foreach($aTable['idname'] as $iIDName)
      {
        $sWhere .= " AND $iIDName=".$aRow[$iIDName];
        $sID .= $aRow[$iIDName];
        if(!empty($aTable['histidname']) && $aTable['histidname'] == $iIDName)
        {
          $iPrimID = $aRow[$iIDName];
        }        
      }
      if(empty($iPrimID)) $iPrimID = $sID;
      $this->oDB->query("UPDATE {$aTable['table']} SET CaseID = $iCaseID WHERE 1=1 $sWhere");
      foreach($aActivities as $aRowAct)
      {
        $sQueryIns = "INSERT INTO eventlog(EventID, Timestamp, Aktivitaeten_ID, CaseID) "
                . "SELECT CONCAT({$sIdName}) AS ID, {$aRowAct['TSFeld']}, {$aRowAct['ID']}, $iCaseID FROM {$aRowAct['tabelle']} WHERE 1=1 $sWhere;";
        $this->oDB->query($sQueryIns);
      }
      // Änderungshistorie für die jeweilige Tabelle mit berücksichtigen
      $this->oDB->query("UPDATE Aenderungshistorie SET CaseID = $iCaseID WHERE Tabelle = '{$aTable['table']}' AND ID = {$sID}");
     
      $sQueryAH = "INSERT INTO eventlog(EventID, Timestamp, Aktivitaeten_ID, CaseID) 
                    SELECT AenderNr, AenderTS, aktivitaeten.ID, CaseID FROM
                    aenderungshistorie INNER JOIN aktivitaeten
                    ON aenderungshistorie.Tabelle = aktivitaeten.wert1
                    AND aenderungshistorie.Feld = aktivitaeten.wert2
                    AND 
                    (IF(spalte3 LIKE 'Wert_neu', LENGTH(Wert_neu) , 1))
                    AND 
                    (IF(spalte3 LIKE 'Wert_alt', LENGTH(Wert_alt) , 1))
                    WHERE aenderungshistorie.Tabelle = '{$aTable['table']}' AND aenderungshistorie.ID = {$iPrimID};";
      $this->oDB->query($sQueryAH);
      echo($sQueryAH."<br>");
      echo($this->oDB->affected_rows." $iPrimID $sID<br>");
     
      foreach($aTable['children'] as $aChildTable)
      {
        $this->findCaseTable($aChildTable, $aRow, $iCaseID);
      }
    }
  }
  
  /**
   * Bereinigt die Daten, ergänzt Werte, wo möglich und löscht Datensätze, die nicht zuordenbar sind
   */
  protected function cleanData()
  {
    $sQuery = "DELETE FROM aenderungshistorie WHERE CaseID = 0;"
            . "DELETE FROM bestellposition WHERE CaseID = 0;"
            . "DELETE FROM bestellung WHERE CaseID = 0;"
            . "DELETE FROM eventlog WHERE CaseID = 0;"
            . "DELETE FROM kreditor WHERE CaseID = 0;"
            . "DELETE FROM rechnung WHERE CaseID = 0;"
            . "DELETE FROM wareneingang WHERE CaseID = 0;"
            . "DELETE FROM zahlung WHERE CaseID = 0;";
    $this->oDB->query($sQuery);
  }


  /**
   * Iteriert durch die Tabellen und ruft die rekursive Funktion zur Case-Suche auf
   */
  public function findCases()
  {
    $bAllCasesSet = false;
    $sQuery = "SET FOREIGN_KEY_CHECKS = 0;";
    $this->oDB->query($sQuery);
    $this->oDB->query("TRUNCATE Cases");
    $this->oDB->query('TRUNCATE eventlog');
    
    // Hierarchischer Array der Tabellen
    $aTables = array(      
          array( 'table'=>'Bestellposition', 'idname'=>array('PosNr', 'BestellNr'),'fkname'=>array(), 
              'children'=>array(
                  array('table'=>'Bestellung', 'histidname'=>'BestellNr', 'idname'=>array('BestellNr', 'KredNr'),'fkname'=>array('BestellNr'), 'children'=>array(
                      array('table'=>'Kreditor', 'idname'=>array('KredNr'), 'fkname'=>array('KredNr'),'children'=>array())
                  )),
                  array('table'=>'Wareneingang', 'idname'=>array('ID'),'fkname'=>array('PosNr', 'BestellNr'), 'children'=>array()),
                  array('table'=>'Rechnung', 'fkname'=>array('PosNr', 'BestellNr'),'idname'=>array('RechNr'),
                      'children'=>array(
                          array('table'=>'Zahlung', 'idname'=>array('ID'), 'fkname'=>array('RechNr'), 'children'=>array())
                      ))              
          ))              
       
    );
    foreach($aTables as $aTable)
    {
      $this->findCaseTable($aTable);
    }
  }
  
  /**
   * Importiert die Excel-Daten in die Datenbank
   */
  public function importData()
  {
    // Alle Namen von USR-ID-Feldern in den Excel-Tabellen
    $aUserFields = array(
      'ErstellUSR', 'FreigabeUSR', 'ZahlUSR' ,'AenderUSR'
    );
    // Alle Namen von Timestamp-Feldern
    $aTimestampFields = array(
        'ErstellTS', 'AenderTS', 'EingangTS', 'ZahlTS', 'FreigabeTS', 'EingangsDat', 'RechnungsDatum'
    );
    $oImportExcel = PHPExcel_IOFactory::load(dirname(__FILE__).'/files/data.xlsx');
    $i = 0;
    $sQuery = "SET FOREIGN_KEY_CHECKS = 0;";
    $this->oDB->query($sQuery);
    $sQuery = "TRUNCATE TABLE usr;";
    $this->oDB->query($sQuery);
    if(!$hResult = $this->oDB->query($sQuery))
    {
      echo("Fehler beim Leeren der Tabellen<br>");
      echo($this->oDB->error." ({$this->oDB->errno})<br>");
      echo($sQuery."<br>");
    }
    // Alle Worksheets durchgehen
    foreach($oImportExcel->getWorksheetIterator() as $iWorksheetNum=>$oWorksheet)
    {
      // 1. Worksheet nicht
      if($iWorksheetNum)
      {
        // Worksheet-Name = Tabellenname
        $sQuery = "TRUNCATE TABLE {$this->aSheets[$i]['tablename']};";
        $this->oDB->query($sQuery);
        if(!$hResult = $this->oDB->query($sQuery))
        {
          echo("Fehler beim Leeren der Tabellen<br>");
          echo($this->oDB->error." ({$this->oDB->errno})<br>");
          echo($sQuery."<br>");
        }
        // Zeilenanzahl
        $iHighestRow = $oWorksheet->getHighestRow();
        for($iRow = $this->aSheets[$i]['rowstart']; $iRow<=$iHighestRow; $iRow++)
        {
          $sValues = '';
          $bHasValue = false;
          // Alle Spalten durchgehen
          for($j=0; $j<$this->aSheets[$i]['cols']; $j++)
          {
            $sValues .= empty($sValues) ? '' : ',';
            $sValue = $oWorksheet->getCellByColumnAndRow($j, $iRow)->getValue();
            $bHasValue |= strlen($sValue) > 0;
            if(in_array($this->aSheets[$i]['fields'][$j], $aTimestampFields) && strlen($sValue))
            {
              // Timestamp konvertieren
              $sValue = ($sValue - 25569) * 86400 - 7200;
              $sValues .= 'FROM_UNIXTIME('. $sValue . ')';
            }
            else
            {
              $sValues .= '"' . ($sValue) . '"';
            }
            // User-Tabelle füllen
            if(in_array($this->aSheets[$i]['fields'][$j], $aUserFields) && strlen($sValue))
            {
              $sQuery = "INSERT IGNORE INTO USR VALUES('$sValue')";
              if(!$hResult = $this->oDB->query($sQuery))
              {
                echo("Fehler beim Einfügen in User-Tabelle<br>");
                echo($this->oDB->error." ({$this->oDB->errno})<br>");
              }
            }
          }
          $sFields = implode(',', $this->aSheets[$i]['fields']);
          $sQuery = "INSERT INTO {$this->aSheets[$i]['tablename']}({$sFields}) VALUES($sValues)";
          // Leerzeilen in Excel ignorieren
          if($bHasValue) 
          {
            if(!$hResult = $this->oDB->query($sQuery))
            {
              echo("Fehler beim Einfügen in Tabelle {$aSheets[$i]['tablename']}<br>");
              echo($this->oDB->error." ({$this->oDB->errno})<br>");
              echo($sQuery."<br>");
            }
          }
        }
      }
      $i++;
    }
  }
}