<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class rosee extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    public static function cron5() {
    //public static function cron15() {
        foreach (eqLogic::byType('rosee') as $rosee) {
			log::add('rosee', 'debug', 'pull cron');
			$rosee->getInformations();
		}

	}


    /*     * *********************Methode d'instance************************* */

	public function preUpdate() {

    	if ($this->getConfiguration('temperature') == '') {
    		throw new Exception(__('Le champ temperature ne peut etre vide',__FILE__));
	}

	if ($this->getConfiguration('humidite') == '') {
        	throw new Exception(__('Le champ humidite ne peut etre vide',__FILE__));
    	}
    	
	}

	public function postInsert() {
    	// Ajout d'une commande dans le tableau pour le point de rosée
        $roseeCmd = new roseeCmd();
        $roseeCmd->setName(__('Point de rosée', __FILE__));
        $roseeCmd->setEqLogic_id($this->id);
	$roseeCmd->setLogicalId('rosee');
        $roseeCmd->setConfiguration('data', 'rosee');
        $roseeCmd->setType('info');
        $roseeCmd->setSubType('numeric');
        $roseeCmd->setUnite('°C');
        $roseeCmd->setEventOnly(1);
	$roseeCmd->setIsHistorized(0);
	$roseeCmd->setDisplay('generic_type','GENERIC');
        $roseeCmd->save();

        // Ajout d'une commande dans le tableau pour le point de givrage
        $frostCmd = new roseeCmd();
        $frostCmd->setName(__('Point de givrage', __FILE__));
        $frostCmd->setEqLogic_id($this->id);
	$frostCmd->setLogicalId('givrage');
        $frostCmd->setConfiguration('data', 'frost');
        $frostCmd->setType('info');
        $frostCmd->setSubType('numeric');
        $frostCmd->setUnite('°C');
        $frostCmd->setEventOnly(1);
	$frostCmd->setIsHistorized(0);
	//$frostCmd->setIsVisible(0);
	$frostCmd->setDisplay('generic_type','GENERIC');
        $frostCmd->save();

	// Ajout d'une commande dans le tableau pour l'alerte rosée
        $AlertRoseeCmd = new roseeCmd();
        $AlertRoseeCmd->setName(__('Alerte rosée', __FILE__));
        $AlertRoseeCmd->setEqLogic_id($this->id);
	$AlertRoseeCmd->setLogicalId('alerte_rosee');
        $AlertRoseeCmd->setConfiguration('data', 'alert_r');
        $AlertRoseeCmd->setType('info');
        $AlertRoseeCmd->setSubType('binary');
        $AlertRoseeCmd->setUnite('');
        $AlertRoseeCmd->setEventOnly(1);
	$AlertRoseeCmd->setIsHistorized(0);
	//$AlertRoseeCmd->setIsVisible(1);
	$AlertRoseeCmd->setDisplay('generic_type','DONT');
        $AlertRoseeCmd->save();
        
	// Ajout d'une commande dans le tableau pour l'alerte givrage
        $AlertGivreCmd = new roseeCmd();
        $AlertGivreCmd->setName(__('Alerte givre', __FILE__));
        $AlertGivreCmd->setEqLogic_id($this->id);
	$AlertGivreCmd->setLogicalId('alerte_givre');
        $AlertGivreCmd->setConfiguration('data', 'alert_g');
        $AlertGivreCmd->setType('info');
        $AlertGivreCmd->setSubType('binary');
        $AlertGivreCmd->setUnite('');
        $AlertGivreCmd->setEventOnly(1);
	$AlertGivreCmd->setIsHistorized(0);
	//$AlertGivreCmd->setIsVisible(1);
	$AlertGivreCmd->setDisplay('generic_type','DONT');
        $AlertGivreCmd->save();
        
        // Ajout d'une commande dans le tableau pour l'humidité absolue
        $AbsHumiCmd = new roseeCmd();
        $AbsHumiCmd->setName(__('Humidité absolue', __FILE__));
        $AbsHumiCmd->setEqLogic_id($this->id);
	$AbsHumiCmd->setLogicalId('humidite_absolue');
        $AbsHumiCmd->setConfiguration('data', 'humidite_a');
        $AbsHumiCmd->setType('info');
        $AbsHumiCmd->setSubType('numeric');
        $AbsHumiCmd->setUnite('g/m3');
        $AbsHumiCmd->setEventOnly(1);
	$AbsHumiCmd->setIsHistorized(0);
	//$AbsHumiCmd->setIsVisible(1);
	$AbsHumiCmd->setDisplay('generic_type','EATHER_HUMIDITY');
        $AbsHumiCmd->save();
	}

	/*  **********************Getteur Setteur*************************** */
	public function postUpdate() {
        foreach (eqLogic::byType('rosee') as $rosee) {
            	$rosee->getInformations();
		}
	}

	public function getInformations() {
	$idvirt = str_replace("#","",$this->getConfiguration('temperature'));
	$cmdvirt = cmd::byId($idvirt);
	if (is_object($cmdvirt)) {
		$temperature = $cmdvirt->execCmd();
		log::add('rosee', 'debug', 'Configuration : temperature ' . $temperature);
	} else {
		log::add('rosee', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
	}

	$idvirt = str_replace("#","",$this->getConfiguration('humidite'));
	$cmdvirt = cmd::byId($idvirt);
	if (is_object($cmdvirt)) {
		$humidite = $cmdvirt->execCmd();
		log::add('rosee', 'debug', 'Configuration : humidite ' . $humidite);
	} else {
		log::add('rosee', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
	}
	
	$dpr=$this->getConfiguration('DPR');
	if ($dpr == '') {
		//valeur par défaut du seuil d'alerte rosée = 2°C
		$dpr=2;
	}   
	log::add('rosee', 'debug', 'Configuration : seuil DPR ' . $dpr);
	
	$pression = $this->getConfiguration('pression');
	if ($pression == '') {
    	//valeur par défaut de la pression atmosphérique : 1013.25 hPa
        $pression=1013.25;
	} else {
		$idvirt = str_replace("#","",$this->getConfiguration('pression'));
		$cmdvirt = cmd::byId($idvirt);
		if (is_object($cmdvirt)) {
			$pression = $cmdvirt->execCmd();
		}
	}
	log::add('rosee', 'debug', 'Configuration : pression ' . $pression);
	
	/*
	// valeurs pour test, l'indice de chaleur doit Ãªtre de 53Â°C...
	$temperature = 5.4;
	$humidite = 98.0;
	$pression = 1033.24;
	*/
	
	/* calcul du point de rosee
		paramètres de MAGNUS pour l'air saturé (entre -45°C et +60°C) :
		alpha  = 6.112 hPa
		beta   = 17.62
  		lambda = 243.12 °C
  	*/
  	$alpha = 6.112;
  	$beta = 17.62;
  	$lambda = 243.12;
  	$Terme1 = log($humidite/100);
  	$Terme2 = ($beta * $temperature) / ($lambda + $temperature);
  	$rosee = $lambda * ($Terme1 + $Terme2) / ($beta - $Terme1 - $Terme2);
  	$rosee = round(($rosee), 1);
	if($rosee >= 0.0) {
		$visibleRosee = 1;
  	} else {
  		$visibleRosee = 0;
  	}
	//log::add('rosee', 'debug', 'Rosée ' . $rosee);
	log::add('rosee', 'debug', 'visibleRosee ' . $visibleRosee);
	
  	/* calcul du point de givrage
  		Point de givrage calculé uniquement si la température extérieure est négative
  	*/
	$temp_kelvin = $temperature + 273.15;
	$rosee_kelvin = $rosee + 273.15;
	$frost_kelvin = 2954.61 / $temp_kelvin;
	$frost_kelvin = $frost_kelvin + 2.193665 * log($temp_kelvin);
	$frost_kelvin = $frost_kelvin - 13.3448;
	$frost_kelvin = 2671.02 / $frost_kelvin;
	$frost_kelvin = $frost_kelvin + $rosee_kelvin - $temp_kelvin;
	$frost = $frost_kelvin -273.15;
	$frost = round(($frost), 1);
	if($frost < 0.0) {	
  		$visibleFrost = 1;
  	} else {
  		$visibleFrost = 0;
  	}
	//log::add('rosee', 'debug', 'Givrage ' . $frost);
	log::add('rosee', 'debug', 'visibleFrost ' . $visibleFrost);

	// Calcul des alertes rosée et givrage en fonction du seuil d'alerte
	if ($VisibleRosee == 1) {
		if(($temperature - $rosee) <= $dpr) {
			$alert_r = 1;
		} else {
			$alert_r = 0;
		}
	} else {
		$alert_r = 0;
	}
		
	if ($VisibleFrost == 1) {
		if (($temperature - $frost) <= $dpr) {
			$alert_g = 1;
		} else {
			$alert_g = 0;
		}
	} else {
		$alert_g = 0;
	}
		
	// Calcul de l'humidité absolue
	$terme_pvs1 = 2.7877 + (7.625 * $temperature) / (241.6 + $temperature);
	$pvs = pow(10,$terme_pvs1);											// pression de saturation de la vapeur d'eau
	$pv = ($humidite * $pvs) / 100.0;										// pression partielle de vapeur d'eau
	$pression = $pression * 100.0;											// conversion de la pression en Pa
	$humi_a = 0.622 * ($pv / ($pression - $pv));					        		// Humidité absolue en kg d'eau par kg d'air
	$v = (461.24 * (0.622 + $humi_a) * ($temperature+273.15)) / $pression;		// Volume specifique en m3 / kg
	$p = 1.0 / $v;														// Poids spécifique en kg / m3
	$humi_a_m3 = 1000.0 * $humi_a * $p;									// Humidité absolue en gr / m3

	foreach ($this->getCmd() as $cmd) {
		if($cmd->getConfiguration('data')=="rosee"){
			$cmd->setConfiguration('value', $rosee);
			$cmd->setIsVisible($visibleRosee);
			$cmd->save();
			$cmd->event($rosee);
			log::add('rosee', 'debug', 'Rosée ' . $rosee);
		}
				
		if($cmd->getConfiguration('data')=="frost"){
			$cmd->setConfiguration('value', $frost);
			$cmd->setIsVisible($visibleFrost);
			$cmd->save();
			$cmd->event($frost);
			log::add('rosee', 'debug', 'Givrage ' . $frost);
		}
				
		if($cmd->getConfiguration('data')=="humidite_a"){
			$cmd->setConfiguration('value', $humi_a_m3);
			$cmd->save();
			$cmd->event($humi_a_m3);
			log::add('rosee', 'debug', 'terme_pvs1 ' . $terme_pvs1);
			log::add('rosee', 'debug', 'pvs ' . $pvs);
			log::add('rosee', 'debug', 'pv ' . $pv);
			log::add('rosee', 'debug', 'pression ' . $pression);
			log::add('rosee', 'debug', 'humi_a ' . $humi_a);
			log::add('rosee', 'debug', 'v ' . $v);
			log::add('rosee', 'debug', 'p ' . $p);
			log::add('rosee', 'debug', 'Humidite Absolue ' . $humi_a_m3);
		}
				
		if($cmd->getConfiguration('data')=="alert_r"){
			$old_alert_r = $cmd->execCmd();
			log::add('rosee', 'debug', 'old Alerte rosée ' . $old_alert_r);
			$cmd->setConfiguration('value', $alert_r);
			$cmd->save();
			if ($alert_r!=$old_alert_r) {
				$cmd->setCollectDate('');
				$cmd->event($alert_r);
			}
			log::add('rosee', 'debug', 'Alerte rosée ' . $alert_r);
		}
				
		if($cmd->getConfiguration('data')=="alert_g"){
			$old_alert_g = $cmd->execCmd();
			log::add('rosee', 'debug', 'old Alerte givrage ' . $old_alert_g);
			$cmd->setConfiguration('value', $alert_g);
			$cmd->save();
			if ($alert_g!=$old_alert_g) {
				$cmd->setCollectDate('');
				$cmd->event($alert_g);
			}
			log::add('rosee', 'debug', 'Alerte givrage ' . $alert_g);
		}
	}
        return ;
    }
}

class roseeCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
	public function execute($_options = null) {
	}

}

?>
