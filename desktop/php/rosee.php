<?php

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('rosee');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    	<div class="col-xs-12 eqLogicThumbnailDisplay">
            <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
            <div class="eqLogicThumbnailContainer">
                <div class="cursor eqLogicAction logoPrimary" data-action="add" >
                    <i class="fas fa-plus-circle"></i>
                    <br/>
                    <span>{{Ajouter}}</span>
                </div>
            </div>
            <legend><i class="fas fa-umbrella"></i> {{Mes Points de Rosée}}</legend>
            <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="eqLogicThumbnailContainer">
                <?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                    echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                    echo '<img src="' . $plugin->getPathImgIcon() . '" />';
                    echo '<br>';
                    echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
    </div>
     <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br/>
                <div class="row">
					<div class="col-sm-6">
						<form class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-3 control-label">{{Nom de l'équipement caméra}}</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
										<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement caméra}}"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" >{{Objet parent}}</label>
									<div class="col-sm-6">
										<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
											<option value="">{{Aucun}}</option>
											<?php
											foreach (jeeObject::all() as $object) {
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
											}
											?>
										</select>
									</div>
								</div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Catégorie}}</label>
                                    <div class="col-sm-8">
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
									<label class="col-sm-3 control-label"></label>
									<div class="col-sm-9">
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
									</div>
								</div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Commentaire}}</label>
                                    <div class="col-sm-6">
                                        <textarea class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="comment"></textarea>
                                    </div>
                                </div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6">
                        <form class="form-horizontal">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">{{Température}}</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="temperature"  placeholder="{{Température}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectTempCmd"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label help" data-help="{{%}}">{{Humidité Relative}}</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="humidite" placeholder="{{Humidité Relative %}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectHumiCmd"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label help" data-help="{{Pression atmosphérique réelle sur le site (pas au niveau de la mer). 1013.25 hPa par défaut si non renseigné}}">{{Pression Atmosphérique}}</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="pression" placeholder="{{1013.25}}">
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdActionOther roundedRight" id="bt_selectPresCmd"><i class="fas fa-list-alt"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label help" data-help="{{Seuil de déclenchement de l'alerte rosée, 2°C par défaut (dépression du point de rosée T°-Tr°) A ajuster en fonction des observations locales...}}">{{Seuil (°C)}}</label>
                                    <div class="col-md-1">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="DPR" placeholder="{{2}}">
                                    </div>
                                </div>
                            </fieldset>
                        </form>
					</div>
				</div>
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <br/>
                <table id="table_cmd" class="table table-bordered table-condensed"><thead>
                    <tr>
                        <th style="width: 250px;"> ID</th>
                        <th style="width: 550px;">{{Nom}}</th>
                        <th style="width: 750px;">{{Valeur}}</th>
                        <th style="width: 450px;">{{Unité}}</th>
                        <th style="width: 450px;">{{Paramètres}}</th>
                        <th style="width: 450px;">{{Options}}</th>
                        <th style="width: 350px;"></th>
                    </tr>
                    <tr>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>        
            </div>
            
            
         </div>
    </div>
</div>



<?php include_file('desktop', 'rosee', 'js', 'rosee'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
