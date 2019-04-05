Hermes Hmail Webform Connexion
==============================
Ce module est destiné à connecter un Webform existant à l'API Hmail pour enregistrer/mettre à jour les souscriptions de newsletters Hermès par webservice PHP.


Il ajoute une fonction de submit au bouton submit du formulaire existant. La fonctionnalité d'origine du formulaire reste inchangé.


Il crée un type d'entité de configuration nommé hmail_config.
Créer une entité de ce type pour chaque webform qu'on souhaite connecter à Hmail.

Dépendances
============
Webform

settings.php
============
Le module est basé sur l'API Hmail (route : /api/v1/subscription) et requiert une authentification de type Basi Auth, soit le login/password d'un utilisateur Hmail possédant le rôle "api_client".
Ces credentials doivent être saisis dans le fichier settings.php sous cette forme :

$settings['hmail_credentials'] = array (
    'user' => 'login_du_user',
    'password' => 'passwd_du_user',
);

Configuration
=============
Après installation du module, une page de configuration est disponible ici : /admin/config/hmail_config.

Créer une entité Hmail configuration.

Champs à remplir :

    SETTINGS
    --------
    - Label
    - Machine-readable name
    - Hmail base url : saisir l'url de l'instance Hmail à utiliser (preprod ou prod), exemple : http://hmail.hermes.com
        A noter : dans le cas d'une preprod protégée par HTpsswd, saisir les credentials dans l'url, exemple : http://HT_access_user:HT_access_passwd@hmail.ppr-aws.hermes.com
    - Application origin : saisir la string correspondant au projet qui vous sera fournie par Hermès, exemple : other_defile_reminder. La liste des application origins est disponible sur le site Hmail : /admin/app_origin
    - Csv mapper : saisir la string correspondant au projet qui vous sera fournie par Hermès, exemple : import_magento. La liste des CSV mappers est disponible sur le site Hmail : /admin/csv-mapper
    - Test Email : saisir un email valide qui sera envoyé à Hmail pour tester l'API.
    
A ce stade, un bouton "Test API" est disponible. Il affiche la réponse du webservice. Status + message si erreur.
Une réponse 200 ou 201 est valide.

A noter : si le test est validé, un lien s'affiche : "Visit Hmail subscribtion". Il mène à la souscription enregistrée dans Hmail lors du test pour vérification.
    
    FORM MAPPING
    ------------
    - Altered Webform : nom machine du webform à connecter à Hmail (ex : newsletter). On trouve généralement la liste des webforms du site à l'url /admin/structure/webform
    Une fois ce champ saisi, la liste des champs disponible pour le mappage (select list) se peuple automatiquement en Ajax si le nom du Webform est reconnu.
    - Field Email : seul champ obligatoire. Choisir le champ correspondant dans le select (ex : adresse_mail)
    - Les autres champs sont facultatifs
    
Une fois l'entité enregistrée, le webform est alors connecté à Hmail. Toutes les souscriptions soumises via ce webform seront également envoyées à Hmail, EN PLUS DE LEURS SOUMISSIONS INTERNES À WEBFORM.

Dysfonctionnement
=================
Réponse du bouton Test API (/admin/config/hmail) : STATUS : 403 SERVER ACCESS FORBIDEN

=> Vérifier que le serveur est autorisé à communiquer avec Hmail (apache2 .conf Allow from ...)

=> Vérifier les credentials dans le fichier settings.php

=> Vérifier le rôle de l'utilisateur Hmail (api_client)

Help
====
pascal.manganaro@mydropteam.com
