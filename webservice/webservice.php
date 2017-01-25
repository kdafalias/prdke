<?php

/* 
 * Webservice is called by frontend
 */

include_once(dirname(__FILE__).'/cPMBackend.inc.php');
 $oPMBackend = new cPMBackend();
 $oPMBackend->getEventData();