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
   * Holt alle zusammengehörenden Aktivitäten und füllt das Eventlog
   */
  public function fillEventlog()
  {
    $this->oDB->query('TRUNCATE eventlog');
    $sQuery = "SELECT * FROM aktivitaeten ORDER BY ID";
    $oTable = $this->oDB->query($sQuery);
    while($aRow = $oTable->fetch_assoc())
    {
      $sWhere = '';
      for($i=1; $i<=3; $i++)
      {
        if(!empty($aRow['spalte'.$i]))
        {
          $sWhere .= ' AND '.$aRow['spalte'.$i].'="'.$aRow['wert'.$i].'"';
        }
      }
      $sQueryAkt = "INSERT INTO eventlog(EventID, Timestamp, Aktivitaeten_ID, CaseID) "
              . "SELECT CONCAT({$aRow['IDFeld']}) AS ID, {$aRow['TSFeld']}, {$aRow['ID']}, CaseID FROM {$aRow['tabelle']} WHERE 1=1 $sWhere;";
      $this->oDB->query($sQueryAkt);
      echo($sQueryAkt."<br>");
    }
  }
  
  
  /**
   * Rekursive Funktion
   * 
   * @param array $aTable Unterzweig des Table-Baums
   * @param array $aID IDs des übergeordneten Blatts 
   * @param integer $iParentCaseID Case-ID der Wurzel des Teilbaums
   */
  private function findCaseTable($aTable, $aID = array(), $iParentCaseID = null)
  {
    $sIdName = implode(',',$aTable['idname']);
    $sQuery = "SELECT $sIdName FROM {$aTable['table']} WHERE 1=1 ";    
    foreach($aID as $sIndex=>$iID)
    {
      $sQuery .= " AND $sIndex = $iID";
      echo($sIndex."<br>");
    }
    echo('<pre>');
    print_r($aTable);
    print_r($aID);
    echo('</pre>');    

    echo($sQuery."<br>");
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
      foreach($aTable['idname'] as $iIDName)
      {
        $sWhere .= " AND $iIDName=".$aRow[$iIDName];
        $sID .= $aRow[$iIDName];
      }
      $this->oDB->query("UPDATE {$aTable['table']} SET CaseID = $iCaseID WHERE 1=1 $sWhere");
      $this->oDB->query("UPDATE Aenderungshistorie SET CaseID = $iCaseID WHERE Tabelle = '{$aTable['table']}' AND ID = {$sID}");
      foreach($aTable['children'] as $aChildTable)
      {
        $this->findCaseTable($aChildTable, $aRow, $iCaseID);
      }
    }
  }
  
  /**
   * Iteriert durch die Tabellen und ruft die rekursive Funktion zur Case-Suche auf
   */
  public function findCases()
  {
    $bAllCasesSet = false;
    $this->oDB->query("TRUNCATE Cases");
    $aTables = array(
      array('table'=>'Kreditor', 'idname'=>array('KredNr'), 'fkname'=>array(),
          'children'=>array()),
      array('table'=>'Bestellung', 'idname'=>array('BestellNr'), 'fkname'=>array(), 
          'children'=>array(
              array('table'=>'Bestellposition', 'idname'=>array('PosNr', 'BestellNr'),'fkname'=>array('BestellNr'), 'children'=>array()),
              array('table'=>'Wareneingang', 'idname'=>array('ID'),'fkname'=>array('BestellNr'), 'children'=>array()),
              array('table'=>'Rechnung', 'fkname'=>array('BestellNr'),'idname'=>array('RechNr'),
                  'children'=>array(
                      array('table'=>'Zahlung', 'idname'=>array('ID'), 'fkname'=>array('RechNr'), 'children'=>array())
                  )),
              
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
        'ErstellTS', 'AenderTS', 'EingangTS', 'ZahlTS', 'FreigabeTS'
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
    foreach($oImportExcel->getWorksheetIterator() as $iWorksheetNum=>$oWorksheet)
    {
      // 1. Worksheet nicht
      if($iWorksheetNum)
      {
        echo($oWorksheet->getTitle()." $iWorksheetNum<br>");
        $sQuery = "TRUNCATE TABLE {$this->aSheets[$i]['tablename']};";
        $this->oDB->query($sQuery);
        if(!$hResult = $this->oDB->query($sQuery))
        {
          echo("Fehler beim Leeren der Tabellen<br>");
          echo($this->oDB->error." ({$this->oDB->errno})<br>");
          echo($sQuery."<br>");
        }

        $iHighestRow = $oWorksheet->getHighestRow();
        for($iRow = $this->aSheets[$i]['rowstart']; $iRow<=$iHighestRow; $iRow++)
        {
          $sValues = '';
          $bHasValue = false;
          for($j=0; $j<$this->aSheets[$i]['cols']; $j++)
          {
            $sValues .= empty($sValues) ? '' : ',';
            $sValue = $oWorksheet->getCellByColumnAndRow($j, $iRow)->getValue();
            if($this->aSheets[$i]['fields'][$j] == 'AenderTS') echo ($sValue."<br>");
            $bHasValue |= strlen($sValue) > 0;
            if(in_array($this->aSheets[$i]['fields'][$j], $aTimestampFields) && strlen($sValue))
            {
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
          echo($sQuery."<br>");
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