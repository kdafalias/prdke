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
  /**
   * Haupt-Methode
   */
  public function main()
  {
    $aSheets = array(
        array('rowstart'=>4,'cols'=>2, 'tablename'=>'Aktivitaeten', 'fields'=>'ID, Aktivitaet'),
        array('rowstart'=>2,'cols'=>10, 'tablename'=>'Kreditor', 'fields'=>'KredNr, Vname, Nname, Firma, PLZ, Ort, Land, SperrKZ, ErstellUSR, ErstellTS'),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Aenderungshistorie', 'fields'=>'AenderNr, Tabelle, Feld, ID, Wert_alt, WertNeu, AenderTS, URS_USR'),
        array('rowstart'=>2,'cols'=>7, 'tablename'=>'Bestellung', 'fields'=>'BestellNr, KredNr, StornoKZ, ErstelltTS, ErstelltUSR, FreigabeTS, FreigageUSR'),
        array('rowstart'=>2,'cols'=>10, 'tablename'=>'Bestellposition', 'fields'=>'PosNr, BestellNr, MaterialNr, Menge, Meinheit, Preis, Waehrung, StornoKZ, ErstelltTS, ErstelltUSR'),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Wareneingang', 'fields'=>'ID; PosNr, BestellNr, Menge, Meinheit, Eingang, EingangUSR, Kreditor_KredNr'),
        array('rowstart'=>2,'cols'=>8, 'tablename'=>'Rechnung', 'fields'=>'RechNr, PosNr, BestellNr, EingangsDat, RechnungsDatum, Betrag, Waehrung, KredNr'),
        array('rowstart'=>2,'cols'=>7, 'tablename'=>'Zahlung', 'fields'=>'ID, RechNr, Betrag, Waehrung, ZahlTS, ZahlUSR, KredNr')        
    );
    $oImportExcel = PHPExcel_IOFactory::load(dirname(__FILE__).'/files/data.xlsx');
    $i = 0;
    foreach($oImportExcel->getWorksheetIterator() as $oWorksheet)
    {
      echo($oWorksheet->getTitle()."<br>");
      $iHighestRow = $oWorksheet->getHighestRow();
      for($iRow = $aSheets[$i]['rowstart']; $iRow<=$iHighestRow; $iRow++)
      {
        $sValues = '';
        for($j=0; $j<$aSheets[$i]['cols']; $j++)
        {
          $sValues .= empty($sValues) ? '' : ',';
          $sValues .= '"' . $oWorksheet->getCellByColumnAndRow($j, $iRow) . '"';
        }
        $sQuery = "INSERT INTO {$aSheets[$i]['tablename']}({$aSheets[$i]['fields']}) VALUES($sValues)";
        $this->oDB->query($sQuery);
      }
      $i++;
    }
  }
}