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
/* Procedure structure for procedure `extract_events` */

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `extract_events`()
BEGIN
		DECLARE finished INTEGER DEFAULT 0;
		DECLARE runtime, totalRuntime FLOAT DEFAULT 0.0;
		DECLARE EL_ID, Akt_act, Akt_before, Anz_akt INTEGER DEFAULT 0;
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
				CALL save_events(Case_before, procString, totalRuntime, Anz_akt);
				LEAVE get_events;
			END IF;						
			IF Case_before = Case_act THEN 
				SET runtime = TIMESTAMPDIFF(HOUR, TS_before, TS_act);
				SET totalRuntime = totalRuntime + runtime;
				SET procString = CONCAT(procString,Akt_act,";");
				SET Anz_akt = Anz_akt+1;
			ELSE 
				IF Case_before > 0 THEN
					CALL save_events(Case_before, procString, totalRuntime, Anz_akt);
				END IF;
				SET Anz_akt = 1;
				SET procString = CONCAT(Akt_act,";");
				SET runtime = 0;
				SET totalRuntime = 0;
				SET Akt_before = 1;
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





















,
	IN `Anz_akt` INT

)
BEGIN
	DECLARE ProcvarID INTEGER DEFAULT 0;
	SELECT ProzessvariantenID INTO ProcvarID FROM prozessvarianten WHERE CompareValue LIKE procString;
	IF ProcvarID > 0 THEN
		UPDATE prozessvarianten SET Haeufigkeit = Haeufigkeit + 1, Durchlaufzeit = (((Durchlaufzeit * (Haeufigkeit - 1)) + totalRuntime)/Haeufigkeit), numActivities=Anz_akt WHERE ProzessvariantenID = ProcvarID;

		UPDATE prozessteilschritte p INNER JOIN teilschritt_tmp t
		ON p.Aktivitaeten_ID = t.aktiv
		AND p.Aktivitaeten_VG_ID = t.aktiv_vg
		SET p.Durchlaufzeit = (p.Durchlaufzeit * Haeufigkeit + t.durchlaufzeit)/(Haeufigkeit+1), Haeufigkeit = Haeufigkeit+1
		WHERE ProzessvariantenID = ProcvarID
		AND CaseID = Case_ID;
	ELSE
		INSERT INTO prozessvarianten (Haeufigkeit, Durchlaufzeit, CompareValue, numActivities) VALUES(1, totalRuntime, procString, Anz_akt);
		SET ProcvarID = LAST_INSERT_ID();
		INSERT INTO prozessteilschritte (Durchlaufzeit, Haeufigkeit, Aktivitaeten_ID, Aktivitaeten_VG_ID, ProzessvariantenID, Knotentyp)
		SELECT durchlaufzeit, 1, aktiv, aktiv_vg, ProcvarID, IF(aktiv_vg > 0, "normal", "start") FROM teilschritt_tmp WHERE CaseID = Case_ID ORDER BY ID;
		UPDATE prozessteilschritte SET Knotentyp = "end" ORDER BY ProzessteilschrittID DESC LIMIT 1;
	END IF;
	DELETE FROM teilschritt_tmp WHERE CaseID = Case_ID;
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
