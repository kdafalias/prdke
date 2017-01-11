/*
SQLyog Community v12.3.2 (64 bit)
MySQL - 10.1.14-MariaDB : Database - prdke
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `aenderungshistorie` */

CREATE TABLE `aenderungshistorie` (
  `AenderNr` int(10) unsigned NOT NULL,
  `Tabelle` varchar(20) DEFAULT NULL,
  `Feld` varchar(20) DEFAULT NULL,
  `ID` int(10) unsigned DEFAULT NULL,
  `Wert_alt` varchar(45) DEFAULT NULL,
  `Wert_neu` varchar(45) DEFAULT NULL,
  `AenderTS` datetime DEFAULT NULL,
  `AenderUSR` varchar(15) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`AenderNr`),
  KEY `fk_Aenderungshistorie_USR1_idx` (`AenderUSR`),
  KEY `fk_Aenderungshistorie_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Aenderungshistorie_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Aenderungshistorie_USR1` FOREIGN KEY (`AenderUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `aktivitaeten` */

CREATE TABLE `aktivitaeten` (
  `ID` int(11) NOT NULL,
  `Aktivitaet` varchar(45) DEFAULT NULL,
  `tabelle` varchar(20) DEFAULT NULL,
  `spalte1` varchar(20) DEFAULT NULL,
  `wert1` varchar(20) DEFAULT NULL,
  `spalte2` varchar(20) DEFAULT NULL,
  `wert2` varchar(20) DEFAULT NULL,
  `spalte3` varchar(20) DEFAULT NULL,
  `wert3` varchar(20) DEFAULT NULL,
  `TSFeld` varchar(20) DEFAULT NULL,
  `IDFeld` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `bestellposition` */

CREATE TABLE `bestellposition` (
  `PosNr` smallint(6) NOT NULL,
  `BestellNr` int(10) unsigned NOT NULL,
  `MaterialNr` int(11) DEFAULT NULL,
  `Menge` smallint(6) DEFAULT NULL,
  `Meinheit` varchar(15) DEFAULT NULL,
  `Preis` float DEFAULT NULL,
  `Waehrung` varchar(5) DEFAULT NULL,
  `StornoKZ` char(1) DEFAULT NULL,
  `ErstellTS` datetime DEFAULT NULL,
  `ErstellUSR` varchar(15) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PosNr`,`BestellNr`),
  KEY `fk_Bestellposition_Bestellung1_idx` (`BestellNr`),
  KEY `fk_Bestellposition_USR1_idx` (`ErstellUSR`),
  KEY `fk_Bestellposition_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Bestellposition_Bestellung1` FOREIGN KEY (`BestellNr`) REFERENCES `bestellung` (`BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bestellposition_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bestellposition_USR1` FOREIGN KEY (`ErstellUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `bestellung` */

CREATE TABLE `bestellung` (
  `BestellNr` int(10) unsigned NOT NULL,
  `KredNr` int(11) DEFAULT NULL,
  `StornoKZ` char(1) DEFAULT NULL,
  `ErstellTS` datetime DEFAULT NULL,
  `ErstellUSR` varchar(15) DEFAULT NULL,
  `FreigabeTS` datetime DEFAULT NULL,
  `FreigabeUSR` varchar(15) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`BestellNr`),
  KEY `fk_Bestellung_Kreditor_idx` (`KredNr`),
  KEY `fk_Bestellung_USR1_idx` (`ErstellUSR`),
  KEY `fk_Bestellung_USR2_idx` (`FreigabeUSR`),
  KEY `fk_Bestellung_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Bestellung_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bestellung_Kreditor` FOREIGN KEY (`KredNr`) REFERENCES `kreditor` (`KredNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bestellung_USR1` FOREIGN KEY (`ErstellUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bestellung_USR2` FOREIGN KEY (`FreigabeUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cases` */

CREATE TABLE `cases` (
  `CaseID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CaseID`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8;

/*Table structure for table `eventlog` */

CREATE TABLE `eventlog` (
  `EventlogID` int(11) NOT NULL AUTO_INCREMENT,
  `EventID` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT NULL,
  `Aktivitaeten_ID` int(11) NOT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EventlogID`),
  KEY `fk_Eventlog_Aktivitaeten1_idx` (`Aktivitaeten_ID`),
  KEY `fk_Eventlog_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Eventlog_Aktivitaeten1` FOREIGN KEY (`Aktivitaeten_ID`) REFERENCES `aktivitaeten` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Eventlog_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=960 DEFAULT CHARSET=utf8;

/*Table structure for table `kreditor` */

CREATE TABLE `kreditor` (
  `KredNr` int(11) NOT NULL,
  `Vname` varchar(45) DEFAULT NULL,
  `Nname` varchar(45) DEFAULT NULL,
  `Firma` varchar(45) DEFAULT NULL,
  `PLZ` varchar(20) DEFAULT NULL,
  `Ort` varchar(45) DEFAULT NULL,
  `Land` varchar(4) DEFAULT NULL,
  `SperrKZ` varchar(45) DEFAULT NULL,
  `ErstellUSR` varchar(15) DEFAULT NULL,
  `ErstellTS` datetime DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`KredNr`),
  KEY `fk_Kreditor_USR1_idx` (`ErstellUSR`),
  KEY `fk_Kreditor_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Kreditor_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Kreditor_USR1` FOREIGN KEY (`ErstellUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `prozessteilschritte` */

CREATE TABLE `prozessteilschritte` (
  `ProzessteilschrittID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Durchlaufzeit` float DEFAULT NULL,
  `Haeufigkeit` int(11) DEFAULT NULL,
  `Knotentyp` set('Start','Standard','End') DEFAULT NULL,
  `Aktivitaeten_ID` int(11) NOT NULL,
  `Aktivitaeten_VG_ID` int(11) DEFAULT NULL,
  `NachfolgerID` int(11) DEFAULT NULL,
  `ProzessvariantenID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ProzessteilschrittID`,`ProzessvariantenID`),
  KEY `fk_Prozessvarianten_Aktivitaeten1_idx` (`Aktivitaeten_ID`),
  KEY `fk_Prozessteilschritte_Prozessvarianten1_idx` (`ProzessvariantenID`),
  KEY `fk_Prozessteilschritte_Aktivitaeten1_idx` (`Aktivitaeten_VG_ID`),
  CONSTRAINT `fk_Prozessteilschritte_Aktivitaeten` FOREIGN KEY (`Aktivitaeten_ID`) REFERENCES `aktivitaeten` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Prozessteilschritte_Prozessvarianten1` FOREIGN KEY (`ProzessvariantenID`) REFERENCES `prozessvarianten` (`ProzessvariantenID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=448 DEFAULT CHARSET=utf8;

/*Table structure for table `prozessvarianten` */

CREATE TABLE `prozessvarianten` (
  `ProzessvariantenID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Haeufigkeit` int(10) unsigned DEFAULT NULL,
  `Durchlaufzeit` float DEFAULT NULL,
  `CompareValue` text,
  PRIMARY KEY (`ProzessvariantenID`),
  KEY `compareValue` (`CompareValue`(255))
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

/*Table structure for table `rechnung` */

CREATE TABLE `rechnung` (
  `RechNr` int(11) NOT NULL,
  `PosNr` smallint(6) NOT NULL,
  `BestellNr` int(10) unsigned NOT NULL,
  `EingangsDat` datetime DEFAULT NULL,
  `RechnungsDatum` datetime DEFAULT NULL,
  `Betrag` float DEFAULT NULL,
  `Waehrung` varchar(5) DEFAULT NULL,
  `KredNr` int(11) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`RechNr`),
  KEY `fk_Rechnung_Bestellposition1_idx` (`PosNr`,`BestellNr`),
  KEY `fk_Rechnung_Kreditor1_idx` (`KredNr`),
  KEY `fk_Rechnung_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Rechnung_Bestellposition1` FOREIGN KEY (`PosNr`, `BestellNr`) REFERENCES `bestellposition` (`PosNr`, `BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Rechnung_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Rechnung_Kreditor1` FOREIGN KEY (`KredNr`) REFERENCES `kreditor` (`KredNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `teilschritt_tmp` */

CREATE TABLE `teilschritt_tmp` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `durchlaufzeit` float DEFAULT NULL,
  `aktiv` int(11) DEFAULT NULL,
  `aktiv_vg` int(11) DEFAULT NULL,
  `CaseID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MEMORY AUTO_INCREMENT=1375 DEFAULT CHARSET=utf8;

/*Table structure for table `usr` */

CREATE TABLE `usr` (
  `USR` varchar(15) NOT NULL,
  PRIMARY KEY (`USR`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `wareneingang` */

CREATE TABLE `wareneingang` (
  `ID` int(11) NOT NULL,
  `PosNr` smallint(6) NOT NULL,
  `BestellNr` int(10) unsigned NOT NULL,
  `Menge` smallint(6) DEFAULT NULL,
  `Meinheit` varchar(5) DEFAULT NULL,
  `EingangTS` datetime DEFAULT NULL,
  `EingangUSR` varchar(15) DEFAULT NULL,
  `Kreditor_KredNr` int(11) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`,`PosNr`,`BestellNr`),
  KEY `fk_Wareneingang_Kreditor1_idx` (`Kreditor_KredNr`),
  KEY `fk_Wareneingang_Bestellposition1_idx` (`PosNr`,`BestellNr`),
  KEY `fk_Wareneingang_USR1_idx` (`EingangUSR`),
  KEY `fk_Wareneingang_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Wareneingang_Bestellposition1` FOREIGN KEY (`PosNr`, `BestellNr`) REFERENCES `bestellposition` (`PosNr`, `BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Wareneingang_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Wareneingang_Kreditor1` FOREIGN KEY (`Kreditor_KredNr`) REFERENCES `kreditor` (`KredNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Wareneingang_USR1` FOREIGN KEY (`EingangUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `zahlung` */

CREATE TABLE `zahlung` (
  `ID` int(11) NOT NULL,
  `RechNr` int(11) DEFAULT NULL,
  `Betrag` float DEFAULT NULL,
  `Waehrung` varchar(5) DEFAULT NULL,
  `ZahlTS` datetime DEFAULT NULL,
  `ZahlUSR` varchar(15) DEFAULT NULL,
  `KredNr` int(11) DEFAULT NULL,
  `CaseID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_Zahlung_Rechnung1_idx` (`RechNr`),
  KEY `fk_Zahlung_USR1_idx` (`ZahlUSR`),
  KEY `fk_Zahlung_Kreditor1_idx` (`KredNr`),
  KEY `fk_Zahlung_Case1_idx` (`CaseID`),
  CONSTRAINT `fk_Zahlung_Case1` FOREIGN KEY (`CaseID`) REFERENCES `cases` (`CaseID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zahlung_Kreditor1` FOREIGN KEY (`KredNr`) REFERENCES `kreditor` (`KredNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zahlung_Rechnung1` FOREIGN KEY (`RechNr`) REFERENCES `rechnung` (`RechNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Zahlung_USR1` FOREIGN KEY (`ZahlUSR`) REFERENCES `usr` (`USR`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `eventlogview` */

DROP TABLE IF EXISTS `eventlogview`;

/*!50001 CREATE TABLE  `eventlogview`(
 `EventlogID` int(11) ,
 `EventID` int(11) ,
 `Timestamp` datetime ,
 `Aktivitaet` varchar(45) ,
 `CaseID` int(10) unsigned 
)*/;

/*View structure for view eventlogview */

/*!50001 DROP TABLE IF EXISTS `eventlogview` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `eventlogview` AS (select `eventlog`.`EventlogID` AS `EventlogID`,`eventlog`.`EventID` AS `EventID`,`eventlog`.`Timestamp` AS `Timestamp`,`aktivitaeten`.`Aktivitaet` AS `Aktivitaet`,`eventlog`.`CaseID` AS `CaseID` from (`eventlog` join `aktivitaeten` on((`eventlog`.`Aktivitaeten_ID` = `aktivitaeten`.`ID`))) order by `eventlog`.`CaseID`,`eventlog`.`Timestamp`) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
