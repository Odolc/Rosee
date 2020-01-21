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
		foreach (eqLogic::byType('rosee') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('rosee', 'debug', '================= CRON 5 ==================');
				$rosee->getInformations();
			}
		}
	}

	public static function cron30($_eqlogic_id = null) {
		//no both cron5 and cron30 enabled:
		if (config::byKey('functionality::cron5::enable', 'rosee', 0) == 1)
		{
			config::save('functionality::cron30::enable', 0, 'rosee');
			return;
		}
		foreach (eqLogic::byType('rosee') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('rosee', 'debug', '================= CRON 30 =================');
				$rosee->getInformations();
			}
		}
	}

	/*     * *********************Methode d'instance************************* */
	public function refresh() {
        foreach ($this->getCmd() as $cmd)
        {
            $s = print_r($cmd, 1);
            log::add('rosee', 'debug', 'refresh  cmd: '.$s);
            $cmd->execute();
        }
    }

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
            $roseeCmd->setConfiguration('data', 'rosee_point');
            $roseeCmd->setType('info');
            $roseeCmd->setSubType('numeric');
            $roseeCmd->setUnite('°C');
            $roseeCmd->setIsHistorized(0);
            $roseeCmd->setDisplay('generic_type','');
            $roseeCmd->save();

		// Ajout d'une commande dans le tableau pour le point de givrage
            $frostCmd = new roseeCmd();
            $frostCmd->setName(__('Point de givrage', __FILE__));
            $frostCmd->setEqLogic_id($this->id);
            $frostCmd->setLogicalId('givrage');
            $frostCmd->setConfiguration('data', 'frost_point');
            $frostCmd->setType('info');
            $frostCmd->setSubType('numeric');
            $frostCmd->setUnite('°C');
            $frostCmd->setIsHistorized(0);
            $frostCmd->setDisplay('generic_type','');
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
            $AlertRoseeCmd->setIsHistorized(0);
            $AlertRoseeCmd->setDisplay('generic_type','GENERIC_INFO');
            $AlertRoseeCmd->save();

		// Ajout d'une commande dans le tableau pour l'alerte givrage
            $AlertGivreCmd = new roseeCmd();
            $AlertGivreCmd->setName(__('Alerte givre', __FILE__));
            $AlertGivreCmd->setEqLogic_id($this->id);
            $AlertGivreCmd->setLogicalId('alerte_givre');
            $AlertGivreCmd->setConfiguration(   'data', 'alert_g');
            $AlertGivreCmd->setType('info');
            $AlertGivreCmd->setSubType('binary');
            $AlertGivreCmd->setUnite('');
            $AlertGivreCmd->setIsHistorized(0);
            $AlertGivreCmd->setDisplay('generic_type','GENERIC_INFO');
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
            $AbsHumiCmd->setIsHistorized(0);
            $AbsHumiCmd->setDisplay('generic_type','WEATHER_HUMIDITY');
            $AbsHumiCmd->save();
	}

	public function postSave(){
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new roseeCmd();
            $refresh->setLogicalId('refresh');
            $refresh->setIsVisible(1);
            $refresh->setName(__('Rafraichir', __FILE__));
        }
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->setEqLogic_id($this->getId());
        $refresh->save();
    }

	/*  **********************Getteur Setteur*************************** */
	public function postUpdate() {
		foreach (eqLogic::byType('rosee') as $rosee) {
				$rosee->getInformations();
		}
	}

	public function getInformations() {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
		log::add('rosee', 'debug', '================= CONFIGURATION : ' .$_eqName.' =================');
        /*  ********************** TEMPERATURE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('temperature'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $temperature = $cmdvirt->execCmd();
                log::add('rosee', 'debug', 'Temperature : ' . $temperature.' °C');
            } else {
                log::add('rosee', 'error', 'Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
            }
      
        /*  ********************** HUMIDITE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('humidite'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $humidite = $cmdvirt->execCmd();
                log::add('rosee', 'debug', 'Humidite : ' . $humidite.' ');
            } else {
                log::add('rosee', 'error', 'Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
            }

        /*  ********************** PRESSION *************************** */
            $pression = $this->getConfiguration('pression');
            if ($pression == '') {
                //valeur par défaut de la pression atmosphérique : 1013.25 hPa
                    $pression=1013.25;
                    log::add('rosee', 'debug', 'Pression aucun équipement de sélectionner ');
                    log::add('rosee', 'debug', 'Pression par défaut : ' . $pression. ' hPa');
            } else {
                $idvirt = str_replace("#","",$this->getConfiguration('pression'));
                $cmdvirt = cmd::byId($idvirt);
                if (is_object($cmdvirt)) {
                    $pression = $cmdvirt->execCmd();
                    log::add('rosee', 'debug', 'Pression : ' . $pression.' hPa');
                } else {
                    log::add('rosee', 'error', 'Configuration : Pression non existante : ' . $this->getConfiguration('pression'));
                }
            }
		 
        /*  ********************** SEUIL D'ALERTE ROSEE *************************** */          
            $dpr=$this->getConfiguration('DPR');
            if ($dpr == '') {
                //valeur par défaut du seuil d'alerte rosée = 2°C
                $dpr=2.0;
                log::add('rosee', 'debug', 'Seuil DPR Aucune valeur de saisie');
                log::add('rosee', 'debug', 'Seuil DPR par défaut : ' . $dpr);       
		      } else {
                log::add('rosee', 'debug', 'Seuil DPR : ' . $dpr.' °C'); 
            }

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
            $rosee_point = round(($rosee), 1);
        
            // Calcul visibilité Alerte Point de rosée
                  //  if ($rosee_point >= 0.0) {
                 //       $visible_Rosee = 1;
                //    } else {
                //        $visible_Rosee = 0;
                //    }

            log::add('rosee', 'debug', '========= CALCUL DU POINT DE ROSEE ========');
            log::add('rosee', 'debug', 'Point de Rosée : ' . $rosee_point);
        //    log::add('rosee', 'debug', 'Visibilité Point de Rosée : ' . $visible_Rosee);
        
        // Calcul de l'alerte rosée en fonction du seuil d'alerte
            $frost_alert_rosee = $temperature - $rosee_point;
                log::add('rosee', 'debug', 'Calcul point de rosee (Température - point de Rosée) : ' . $frost_alert_rosee );
        
           // if ($visible_Rosee == 1) {
                if (($frost_alert_rosee) <= $dpr) {
                    $alert_r = 1;
                    log::add('rosee', 'debug', 'RESULTAT Calcul point de rosee (Calcul point de Rosée  <= Seuil DPR)');
                } else {
                    $alert_r = 0;
                    log::add('rosee', 'debug', 'RESULTAT Calcul point de rosee (Calcul point de Rosée  > Seuil DPR)');
                }
          //  } else {
              //  $alert_r = 0;
             //   log::add('rosee', 'debug', 'AUCUN Calcul point de Rosée car Visibilité Point de Rosée = 0');
          //  }
		  log::add('rosee', 'debug', 'Etat alerte rosée ' . $alert_r);
        
        
		/* calcul du point de givrage
		*/
            $temp_kelvin = $temperature + 273.15;
            $rosee_kelvin = $rosee + 273.15;
            $frost_kelvin = 2954.61 / $temp_kelvin;
            $frost_kelvin = $frost_kelvin + 2.193665 * log($temp_kelvin);
            $frost_kelvin = $frost_kelvin - 13.3448;
            $frost_kelvin = 2671.02 / $frost_kelvin;
            $frost_kelvin = $frost_kelvin + $rosee_kelvin - $temp_kelvin;
            $frost = $frost_kelvin -273.15;
            $frost_point = round(($frost), 1);
        
            // Calcul visibilité Alerte Point de Givrage
                   // if($frost_point < 0.0) {
                     //   $visible_Frost = 1;
                    //} else {
                    //    $visible_Frost = 0;
                //    }

            log::add('rosee', 'debug', '======== CALCUL DU POINT DE GIVRAGE =======');
            log::add('rosee', 'debug', 'Point de Givrage :' . $frost_point.' °C');
          //  log::add('rosee', 'debug', 'Visibilité Point de Givrage : ' . $visible_Frost);

        // Calcul de l'alerte givrage en fonction du seuil d'alerte
            $frost_alert_givrage = $temperature - $frost_point;
                log::add('rosee', 'debug', 'Calcul point de givrage (Température - point de givrage) : ' . $frost_alert_givrage);
        
           // if ($visible_Frost == 1) {
                if (($frost_alert_givrage) <= $dpr) {
                    $alert_g = 1;
                    log::add('rosee', 'debug', 'RESULTAT Calcul point de givrage (Calcul point de givrage  <= Seuil DPR)');
                } else {
                    $alert_g = 0;
                    log::add('rosee', 'debug', 'RESULTAT Calcul point de givrage (Calcul point de givrage  > Seuil DPR)');
                }
            //} else {
              //  $alert_g = 0;
            //    log::add('rosee', 'debug', 'AUCUN Calcul point de givrage car Visibilité Point de Givrage = 0');
            //}
        
            log::add('rosee', 'debug', 'Etat alerte gel : ' . $alert_g);

		// Calcul de l'humidité absolue
            $terme_pvs1 = 2.7877 + (7.625 * $temperature) / (241.6 + $temperature);
            $pvs = pow(10,$terme_pvs1);                                             // pression de saturation de la vapeur d'eau
            $pv = ($humidite * $pvs) / 100.0;                                       // pression partielle de vapeur d'eau
            $pression = $pression * 100.0;                                          // conversion de la pression en Pa
            $humi_a = 0.622 * ($pv / ($pression - $pv));                            // Humidité absolue en kg d'eau par kg d'air
            $v = (461.24 * (0.622 + $humi_a) * ($temperature+273.15)) / $pression;  // Volume specifique en m3 / kg
            $p = 1.0 / $v;                                                          // Poids spécifique en kg / m3
            $humi_a_m3 = 1000.0 * $humi_a * $p;                                     // Humidité absolue en gr / m3
            $humi_a_m3 = round(($humi_a_m3), 1);
                

                log::add('rosee', 'debug', '========= CALCUL DE L HUMIDITE ABSOLUE ========');
                log::add('rosee', 'debug', 'terme_pvs1 : ' . $terme_pvs1);
                log::add('rosee', 'debug', 'pvs : ' . $pvs);
                log::add('rosee', 'debug', 'pv : ' . $pv);
                log::add('rosee', 'debug', 'Pression : ' . $pression.' Pa');
                log::add('rosee', 'debug', 'humi_a : ' . $humi_a);
                log::add('rosee', 'debug', 'v : ' . $v);
                log::add('rosee', 'debug', 'p : ' . $p);
                log::add('rosee', 'debug', 'Humidite Absolue : ' . $humi_a_m3);


        log::add('rosee', 'debug', '=============== MISE A JOUR ===============');

		$cmd = $this->getCmd('info', 'alerte_givre');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_g);
			$cmd->save();
			$cmd->setCollectDate('');
			$cmd->event($alert_g);
                log::add('rosee', 'debug', 'Etat alerte givrage : ' . $alert_g);
		}
		$cmd = $this->getCmd('info', 'alerte_rosee');
		if(is_object($cmd)) {
			$cmd->setConfiguration('value', $alert_r);
			$cmd->save();
			$cmd->setCollectDate('');
			$cmd->event($alert_r);
                log::add('rosee', 'debug', 'Etat alerte rosée : ' . $alert_r);
		}
		$cmd = $this->getCmd('info', 'humidite_absolue');
		if(is_object($cmd)) {
			$cmd->setConfiguration('value', $humi_a_m3);
			$cmd->save();
			$cmd->event($humi_a_m3);
                log::add('rosee', 'debug', 'Humidite Absolue : ' . $humi_a_m3);
		}
		$cmd = $this->getCmd('info', 'givrage');
		if(is_object($cmd)) {
			$cmd->setConfiguration('value', $frost_point);
			$cmd->save();
			$cmd->event($frost_point);
            //$AlertRoseeCmd->setIsVisible(0);
                log::add('rosee', 'debug', 'Givrage : ' . $frost_point.' °C');
		}
		$cmd = $this->getCmd('info', 'rosee');
		if(is_object($cmd)) {
			$cmd->setConfiguration('value', $rosee_point);
			$cmd->save();
			$cmd->event($rosee_point);
			//$cmd->setIsVisible($visible_Rosee);
                log::add('rosee', 'debug', 'Rosée : ' . $rosee_point.' °C');
		 }
        log::add('rosee', 'debug', '================= FIN CONFIGURATION : ' .$_eqName.' =================');
		return;
	}
}

class roseeCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */
	public function dontRemoveCmd()
    {
        return true;
    }

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->getInformations();
			return;
		}
	}
}

?>