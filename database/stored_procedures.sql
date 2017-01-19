/*
SQLyog Community v12.3.3 (64 bit)
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
) ENGINE=InnoDB AUTO_INCREMENT=1052 DEFAULT CHARSET=utf8;

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
  `Knotentyp` set('start','normal','end') DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8577 DEFAULT CHARSET=utf8;

/*Table structure for table `prozessvarianten` */

CREATE TABLE `prozessvarianten` (
  `ProzessvariantenID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Haeufigkeit` int(10) unsigned DEFAULT NULL,
  `Durchlaufzeit` float DEFAULT NULL,
  `CompareValue` text,
  `numActivities` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ProzessvariantenID`),
  KEY `compareValue` (`CompareValue`(255)),
  KEY `numActivities` (`numActivities`)
) ENGINE=InnoDB AUTO_INCREMENT=802 DEFAULT CHARSET=utf8;

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
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

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

/* Procedure structure for procedure `extract_events` */

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `extract_events`()
BEGIN
		DECLARE finished INTEGER DEFAULT 0;
		DECLARE runtime, totalRuntime FLOAT DEFAULT 0.0;
		DECLARE EL_ID, Akt_act, Akt_before INTEGER DEFAULT 0;
		DECLARE TS_act, TS_before TIMESTAMP DEFAULT null;
		DECLARE Case_act, Case_before INTEGER DEFAULT 0;
		DECLARE procString VARCHAR(50000) DEFAULT ""; 
		
		DECLARE event_cursor CURSOR FOR SELECT EventlogID, `Timestamp`, Aktivitaeten_ID, CaseID FROM `eventlog` 
		ORDER BY CaseID, `Timestamp`;
		
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

		DELETE FROM teilschritt_tmp;
		DELETE FROM prozessteilschritte;
		DELETE FROM prozessvarianten;
		
		OPEN event_cursor;
		get_events: LOOP
			FETCH event_cursor INTO EL_ID, TS_act, Akt_act, Case_act;
			IF finished = 1 THEN 
				CALL save_events(Case_before, procString, totalRuntime);
				LEAVE get_events;
			END IF;						
			IF Case_before = Case_act THEN 
				SET runtime = TIMESTAMPDIFF(HOUR, TS_before, TS_act);
				SET totalRuntime = totalRuntime + runtime;
				SET procString = CONCAT(Akt_before, "_", Akt_act,";",procString);
			ELSE 
				IF Case_before > 0 THEN
					CALL save_events(Case_before, procString, totalRuntime);
				END IF;
				SET runtime = 0;
				SET totalRuntime = 0;
				SET procString = "";
				SET Akt_before = 0;
			END IF;
			INSERT INTO teilschritt_tmp(durchlaufzeit, aktiv, aktiv_vg, CaseID) VALUES(runtime, Akt_act, Akt_before, Case_act);
			SET TS_before = TS_act;
			SET Akt_before = Akt_act;
			SET Case_before = Case_act;
		END LOOP get_events;
		CLOSE event_cursor;
	END */$$
DELIMITER ;

/* Procedure structure for procedure `save_events` */

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `save_events`(
	IN `Case_ID` INT

,
	IN `procString` VARCHAR(50000)










,
	IN `totalRuntime` FLOAT




















)
BEGIN
	DECLARE ProcvarID INTEGER DEFAULT 0;
	SELECT ProzessvariantenID INTO ProcvarID FROM prozessvarianten WHERE CompareValue LIKE procString;
	IF ProcvarID > 0 THEN
		UPDATE prozessvarianten SET Haeufigkeit = Haeufigkeit + 1, Durchlaufzeit = (((Durchlaufzeit * (Haeufigkeit - 1)) + totalRuntime)/Haeufigkeit) WHERE ProzessvariantenID = ProcvarID;

		UPDATE prozessteilschritte p INNER JOIN teilschritt_tmp t
		ON p.Aktivitaeten_ID = t.aktiv
		AND p.Aktivitaeten_VG_ID = t.aktiv_vg
		SET p.Durchlaufzeit = (p.Durchlaufzeit * Haeufigkeit + t.durchlaufzeit)/(Haeufigkeit+1), Haeufigkeit = Haeufigkeit+1
		WHERE ProzessvariantenID = ProcvarID
		AND CaseID = Case_ID;
	ELSE
		INSERT INTO prozessvarianten (Haeufigkeit, Durchlaufzeit, CompareValue) VALUES(1, totalRuntime, procString);
		SET ProcvarID = LAST_INSERT_ID();
		INSERT INTO prozessteilschritte (Durchlaufzeit, Haeufigkeit, Aktivitaeten_ID, Aktivitaeten_VG_ID, ProzessvariantenID, Knotentyp)
		SELECT durchlaufzeit, 1, aktiv, aktiv_vg, ProcvarID, IF(aktiv_vg > 0, "normal", "start") FROM teilschritt_tmp WHERE CaseID = Case_ID ORDER BY ID;
		UPDATE prozessteilschritte SET Knotentyp = "end" ORDER BY ProzessteilschrittID DESC LIMIT 1;
	END IF;
	DELETE FROM teilschritt_tmp WHERE CaseID = Case_ID;
END */$$
DELIMITER ;

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
