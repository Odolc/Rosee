<?php

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'rosee');
$eqLogics = eqLogic::byType('rosee');

?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes Points de Rosée}}
        </legend>
            <div class="eqLogicThumbnailContainer">
                      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
           <center>
            <i class="fa fa-plus-circle" style="font-size : 7em;color:#00979c;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>Ajouter</center></span>
    </div>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    echo '<img src="plugins/rosee/doc/images/rosee_icon.png" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
            </div>
    </div>    
    
    
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <div class="row">
            <div class="col-sm-6">
                <form class="form-horizontal">
            <fieldset>
                <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i>  {{Général}}
                <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
                </legend>
                <div class="form-group">
                    <label class="col-md-2 control-label">{{Nom}}</label>
                    <div class="col-md-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement rosee}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >{{Objet parent}}</label>
                    <div class="col-md-3">
                        <select class="form-control eqLogicAttr" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">{{Catégorie}}</label>
                    <div class="col-md-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>
            	
            	<div class="form-group">
             		<label class="col-sm-2 control-label" ></label>
              		<div class="col-sm-9">

				<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
				<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
              		</div>
            	</div>
                            
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Commentaire}}</label>
                    <div class="col-md-8">
                        <textarea class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="commentaire" ></textarea>
                    </div>
                </div>  
            </fieldset> 
            	
        </form>
        </div>      
        
            <div id="infoNode" class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Informations}}</legend>
             		<div class="form-group">
                    	<label class="col-md-2 control-label">{{Température}}</label>
                    	<div class="col-md-3">
                    		<textarea class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="temperature" style="height : 33px;width : 320px;display : inline-block"  placeholder="{{#}}"></textarea></br>
                    		<span class="input-group-btn">
                            		<a class="btn btn-default cursor" title="Rechercher une commande" id="bt_selectTempCmd"><i class="fa fa-list-alt"></i></a>
                        	</span>
                    	</div>
                	</div>                    		
                	<div class="form-group">
                    	<label class="col-md-2 control-label">{{Humidité Relative %}}</label>
                    	<div class="col-md-3">
                        	<textarea class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="humidite" style="height : 33px;width : 320px;display : inline-block"  placeholder="{{#}}"></textarea></br>
                        	<span class="input-group-btn">
                            	<a class="btn btn-default cursor" title="Rechercher une commande" id="bt_selectHumiCmd"><i class="fa fa-list-alt"></i></a>
                        	</span>
                    	</div>    
                	</div> 
                	
                	<div class="form-group">
                    	<label class="col-md-2 control-label">{{Pression Atmosphérique}}</label>
                    	<div class="col-md-3">
                        	<textarea class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="pression" style="height : 33px;width : 320px;display : inline-block"  placeholder="{{1013.25}}"></textarea></br>
                        	<textarea style="height : 66px;width : 320px;display : inline-block" placeholder="{{Pression atmosphérique réelle sur le site (pas au niveau de la mer). 1013.25 hPa par défaut si non renseigné}}"></textarea></br>
                        	<span class="input-group-btn">
                            	<a class="btn btn-default cursor" title="Rechercher une commande" id="bt_selectPresCmd"><i class="fa fa-list-alt"></i></a>
                        	</span>
                    	</div>
                	</div> 

                	<div class="form-group">
                    	<label class="col-md-2 control-label">{{Seuil (°C)}}</label>
                    	<div class="col-md-1">
                        	<textarea class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="DPR" style="height : 33px;width : 30px;display : inline-block"  placeholder="{{2}}"></textarea>
                    	</div>
                    	Seuil de déclenchement de l'alerte rosée, 2°C par défaut (dépression du point de rosée T°-Tr°) A ajuster en fonction des observations locales...
                	</div>             	          	
                    </fieldset> 
                </form>
            </div>
        </div>

	<legend>{{Point de rosée, givrage, alertes et tendances}}</legend>
		<!--<a class="btn btn-default btn-sm" id="bt_addVirtualInfo"><i class="fa fa-plus-circle"></i> {{Ajouter une info virtuelle}}</a>-->
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 230px;">{{Nom}}</th>
                    <th style="width: 110px;">{{Valeur}}</th>
                    <th style="width: 100px;">{{Unité}}</th>
                    <th style="width: 200px;">{{Paramètres}}</th>
                    <th style="width: 100px;"></th>
                </tr>
                <tr>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php include_file('desktop', 'rosee', 'js', 'rosee'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
