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
$('#bt_selectTempCmd').on('click', function () {
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subType: 'numeric'}}, function (result) {
        $('.eqLogicAttr[data-l2key=temperature]').atCaret('insert', result.human);
    });
});

$('#bt_selectHumiCmd').on('click', function () {
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subType: 'numeric'}}, function (result) {
        $('.eqLogicAttr[data-l2key=humidite]').atCaret('insert', result.human);
    });
});

$('#bt_selectPresCmd').on('click', function () {
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subType: 'numeric'}}, function (result) {
        $('.eqLogicAttr[data-l2key=pression]').atCaret('insert', result.human);
    });
});

$("#table_cmd").delegate(".listEquipementInfo", 'click', function() {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=calcul]');
        calcul.atCaret('insert', result.human);
    });
});

$("#table_cmd").delegate(".listEquipementAction", 'click', function() {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
        calcul.value(result.human);
    });
});

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
    	console.log("add cmd:" + init(_cmd.id))	// ajouté pour debug
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    if (init(_cmd.type) == 'info') {
        var disabled = (init(_cmd.configuration.virtualAction) == '1') ? 'disabled' : '';
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
        tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="name"></span></td>';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="configuration" data-l2key="value"></span>';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unité}}"></td>';
        tr += '</td>';
        tr += '<td>';
        
        //tr += '<span><input type="checkbox" data-size="mini" data-label-text="{{Historiser}}" class="cmdAttr bootstrapSwitch" data-l1key="isHistorized" /></span>';
        //tr += '<span><input type="checkbox" data-size="mini" data-label-text="{{Afficher}}" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" /><br/></span>';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" cLass="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';

        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="eventOnly"' + disabled + ' /> {{Evénement seulement}}<br/></span>';
        
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
        }
        tr += '</td>';
        tr += '</tr>';
        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');

    }

}
