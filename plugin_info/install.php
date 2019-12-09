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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';


function rosee_install() {
    config::save('functionality::cron5::enable', 1, 'rosee');
    config::save('functionality::cron30::enable', 0, 'rosee');
    $cron = cron::byClassAndFunction('rosee', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}

function rosee_update() {
    if (config::byKey('functionality::cron5::enable', 'rosee', -1) == -1)
        config::save('functionality::cron5::enable', 1, 'rosee');
    if (config::byKey('functionality::cron30::enable', 'rosee', -1) == -1)
        config::save('functionality::cron30::enable', 0, 'rosee');
    $cron = cron::byClassAndFunction('rosee', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}

function rosee_remove() {
    $cron = cron::byClassAndFunction('rosee', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
?>