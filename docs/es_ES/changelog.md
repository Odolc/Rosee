---
title: Jeedom | Plugin Rosee
description: Ce plugin permet d'obtenir le point de rosée pour savoir si l'herbe sera mouillée le matin, ou bien en hiver savoir si il va falloir gratter le pare-brise. Pour fonctionner, on doit indiquer un équipement température et un équipement humidité (extérieures, bien-sûr…)
---

## Changelog
>*Remarque : en cas de mise à jour non listée ici, c'est que celle-ci ne comporte que des changements mineurs du type documentation ou corrections de bugs mineurs.*

# Version 3.3

- Correction Bug

# Version 3.2

- Ajout d’un cron 30 (Merci à kiboost)
- Amélioration de l'affichage pour le Core V4 (Merci à kiboost)
- Possibilité de renommer les commandes (Merci à kiboost)
- Correction des historiques (Merci à kiboost)
- Commande Refresh (sur la tuile, scénario etc) (Merci à kiboost)
- Amélioration des logs
- Correction type de generic
- Correction Bug : l'actualisation des données ne se fait plus si l'équipement est désactivé
- Les alertes sont visible par défaut (plus de masquage si l'alerte est à 0)
- Nettoyage des dossiers (Merci à kiboost)
- Mise à jour de la documentation

>*Remarque : Il est conseillé de supprimer le plugin et ensuite le réinstaller*

# Version 3.1

- La recherche des cmd pour mise à jour ne se fait plus par getConfiguration('data') mais par leur logicalId. Les cmd perdent leur data de configuration. (merci à  jpty)

# Version 3.0

- Support de PHP 7.3
- Migration vers font-awesome 5
- Migration affichage au format core V4

# Version 2.1

- Correction affichage point de rosée et givre defaillants

# Version 2.0

- Mise à jour pour compatibilité V3 Jeedom

# Version 1.5.2

- Correction de bug dans rosee.class.php dans l'appel de la fonction cron15() (merci à mika-nt28 et Mika)

# Version 1.5.1

- Correction de bug dans la prise en compte du seuil d’alerte rosée

# Version 1.5

- Gestion des alertes rosée et givre par changement d’état (merci Toregreb)

# Version 1.4

- Seuil d’alerte du point de rosée configurable dans Informations. Valeur par défaut 2°C

# Version 1.3.1

- Réglage du seuil d’alerte du point de rosée et du point de givrage à 2°C (dépression du point de rosée)

# Version 1.3

- Ajout d’une alerte point de rosée et d’une alerte point de givrage

# Version 1.2

- Selection de la temperature et de l’humidité (possibles par un bouton de recherche) (merci Lunarok)

# Version 1.1

- Ajout du point de givre

# Version 1.0

- Création du plugin