<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


$connector_strings = array (
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">Veuillez saisir l&#39;URL de votre serveur IBM Connections.<br>
</td></tr></table>',
    'company_url' => 'URL',
    'LBL_OVERVIEW_TAB' => 'Aperçu',
    'LBL_UPDATES_TAB' => 'Mises à jour',
    'LBL_ACTIVITIES_TAB' => 'Activités',
    'LBL_FILES_TAB' => 'Fichiers',
    'LBL_DISCUSSIONS_TAB' => 'Forums',
    'LBL_BOOKMARKS_TAB' => 'Signets',
    'LBL_BLOG_TAB' => 'Bogues',
    'LBL_WIKI_TAB' => 'Wikis',
    'LBL_MEMBERS_TAB' => 'Membres',
    'LBL_MY_COMMUNITIES_TAB' => 'Mes communautés',
    'LBL_MY_ACCOUNT_TAB' => 'Mon compte',
    'LBL_MY_ACCOUNT' => 'Mon compte',
    'LBL_PUBLIC_COMMUNITIES_TAB' => 'Communautés publiques',
    'LBL_LOADING' => 'Chargement...',
    'LBL_ADD_MEMBER' => 'Ajouter membre',
    'LBL_CREATE_FILE' => 'Créer un fichier',
    'LBL_CREATE_COMMUNITY' => 'Créer une Communauté²',
    
    'LBL_COMMUNITY_ACCESS_PUBLIC' => 'Public ',
    'LBL_COMMUNITY_ACCESS_RESTRICTED' => 'Privé',
    'LBL_COMMUNITY_ACCESS_MODERATED' => 'Modéré',
    
    'LBL_COMMUNITY' => 'Communauté',
    'LBL_PUBLIC' => 'Public',
    'LBL_NAME' => 'Nom',
    'LBL_URL' => 'URL',
    
    'LBL_MEMBER_ROLE_OWNER' => 'Propriétaire',
    'LBL_MEMBER_ROLE_MEMBER' => 'Membre',
    
    
    'LBL_CREATE_BOOKMARK' => 'Nouveau signet',
    'LBL_CREATE_ACTIVITY' => 'Nouvelle activité',

    'LBL_FILE_UPLOADED_SHARED' => 'Fichier téléchargé et partagé!',
    'LBL_FILE_UPLOADED_NOT_SHARED' => 'Fichier téléchargé, mais non partagé.',
    'LBL_COMMUNITY_SELECTION_SAVED' => 'La sélection de la communauté a été sauvegardé!',

    'LBL_NEW_COMMUNITY_BUTTON' => 'Nouvelle communauté',
    'LBL_SELECT_COMMUNITY_BUTTON' => 'Associer une communauté',
    'LBL_ADD_MEMBER_BUTTON' => 'Ajouter un membre',
    'LBL_NEW_FILE_BUTTON' => 'Nouveau fichier',
    'LBL_NEW_ACTIVITY_BUTTON' => 'Nouvelle activité',
    'LBL_NEW_DISCUSSION_BUTTON' => 'Nouveau sujet',
    'LBL_NEW_BOOKMARK_BUTTON' => 'Nouveau signet',
    //'LBL_NEW_FILE_BUTTON' => 'New File',
    

    'LBL_PROBLEM_LOADING_PAGE' => 'Un problème est survenu durant le chargement de cette page',
    'LBL_LAST_UPDATED' => 'Dernière mise à jour il y a ',
    'LBL_UPDATED_BY' => 'Mis à jour par',
    'LBL_UPDATED_ON' => 'Mis à jour il y a',
    'LBL_BY'=> 'par',
    'LBL_CREATED' => 'créé',
    'LBL_UPLOADED' => 'téléchargé il y a ',
    'LBL_DOWNLOADS' => 'Téléchargements',
    'LBL_VIEW' => 'Vue',
    'LBL_NO_DATA' => 'Aucune donnée',
    'LBL_POST_ON' => 'Action rapide',

    'LBL_SEARCH' => 'Rerchercher',
    'LBL_ENTER_SEARCH_PARAMETER' => 'Veuillez saisir vos paramtères de recherche.',
    'LBL_SUBMIT_BUTTON' => 'Envoyer',
    'LBL_CREATE_NEW_COMMUNITY' => 'Céer une communauté',
    'LBL_COMMUNITY_NAME' => 'Nom',
    'LBL_TAGS' => 'Etiquettes',
    'LBL_ACCESS' => 'Accès',
    'LBL_MEMBERS' => 'Membres',
    'LBL_ADD' => 'Ajouter',
    'LBL_IMAGE' => 'Image',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_IS_IMPORTANT' => 'Important',

    'LBL_PLEASE_SELECT_COMMUNITY' => 'Veuillez sélectionner la communauté avec laquelle associer ce ',
    'LBL_SHARE_FILE_WITH_COMMUNITY' => 'Partager un fichier avec la communauté',
    'LBL_FILE_NAME' => 'Fichier',
    'LBL_SHARE_WITH' => 'Partager avec',
    'LBL_ONLY_OWNER_CAN_ASSOCIATE_1' => 'Il n&#39;y a aucune communauté associée à cet enregistrement. Uniquement ',
    'LBL_ONLY_OWNER_CAN_ASSOCIATE_2' => ' peut associer cet enregistrement à une communauté.',
    'LBL_CONFIRM_DELETE' => 'Etes-vous sur de vouloir supprimer cette association ?',
    'LBL_REQUIRED_SYMBOL' => '*',
    'LBL_HELP_TAGS' => 'Une étiquette est un mot clé que vous attribuez à un contenu pour en faciliter la recherche. Les étiquettes doivent être des mots uniques, comme paie ou ressources_humaines, séparés par des virgules ou des espaces.',
    'LBL_HELP_ACCESS' => 'Le niveau d&#39;accès pour une communauté peut avoir l&#39;un des types suivants : Publique (accessible et visible par tous), Modérée (visible par tous, mais vous devez demander pour en devenir membre) ou Restreinte (uniquement visible pour les membres invités ou ajoutés par un propriétaire). Vous pouvez modifier le type d&#39;une communauté à tout moment en utilisant le formulaire Editer la communauté.',
    'LBL_EDIT' => 'Editer',
    'LBL_DELETE' => 'Supprimer',
    'LBL_PUBLIC_ACCESS_DESCRIPTION' => 'Tout le monde peut rejoindre la communauté',
    'LBL_PUBLICINVITEONLY_ACCESS_DESCRIPTION' => 'Les personnes doivent faire une demande pour rejoindre la communauté',
    'LBL_PRIVATE_ACCESS_DESCRIPTION' => 'Les personnes doivent être invitées à rejoindre la communautéy',
    'LBL_MEMBER_BLOGS' => 'Blogues',
    'LBL_MEMBER_FORUMS' => 'Forums',
    'LBL_MEMBER_WIKIS' => 'Wikis',
    'LBL_MEMBER_FILES' => 'Fichiers',
    'LBL_MEMBER_COMMUNITIES' => 'Communautés',
    'LBL_MEMBER_BOOKMARKS' => 'Signets',
    'LBL_MEMBER_PROFILE' => 'Profil',
    'LBL_MEMBER_ACTIVITIES' => 'Activités',
    'LBL_SUBPANEL_NAME' => 'IBM Connections',
    'LBL_LOGIN_LABEL'=> 'Veuillez saisir vos identifiants pour IBM Connections :',
    'LBL_STATUS_NOT_CONNECTED' => 'Non connecté',
    'LBL_CONNECTED' => 'Conencté. Rafraichir la page.',
    'LBL_STATUS' => 'Statut : ',
    'LBL_NOT_CONFIGURED_MESSAGE_P1' => 'Ce module n&#39;est pas correctement configuré. Envoyez un ',//email your SugarCRM Administaror (' ;//requesting they configure or disable the IBM Connections Connector. <br> Click <a href="#" id="ibm_install_doc" target="_blank">here</a> to review the installation documentation.',
    'LBL_NOT_CONFIGURED_MESSAGE_P2' => ' à votre administrateur SugarCRM afin qu&#39;il active et configure le connecteur pour IBM. ',// '<br> Click <a href="#" id="ibm_install_doc" target="_blank">here</a> to review the installation documentation.',
    'LBL_NOT_CONFIGURED_MESSAGE_P3' => ' pour accéder à la documentation d&#39;installation.',
    'LBL_EMAIL' => 'email',
    'LBL_CLICK' => 'Cliquez ici',
    
    'LBL_CONNECT_PROBLEM_AUTH' => 'Identifiant et/ou mot de passe invalides',
    'LBL_CONNECT_PROBLEM_URL' => 'Impossible de se connecter à IBM Connections. Veuillez contacter votre administrateur SugarCRM.',
    'LBL_USER_NAME' => 'Identifiant',
    'LBL_PASSWORD' => 'Mot de passe',
    'LBL_BUTTON_CONNECT' => 'Se connecter',
    'LBL_BUTTON_CONNECT_DESC' => 'Connecter',
    'LBL_PRIVATE_COMMUNITY' => 'Communté privé. Accès interdit.',
    
    'LBL_UPDATES_ALL' => 'Tous',
    'LBL_UPDATES_STATUS' => 'Statuts',
    'LBL_AGO' => 'de cela',
    'LBL_IN' => 'dans',
    'LBL_UNIT_SECOND' => 'seconde',
    'LBL_UNIT_SECONDS' => 'secondes',
    'LBL_UNIT_MINUTE' => 'minute',
    'LBL_UNIT_MINUTES' => 'minutes',
    'LBL_UNIT_HOUR' => 'heure',
    'LBL_UNIT_HOURS' => 'heures',
    'LBL_UNIT_DAY' => 'jour',
    'LBL_UNIT_DAYS' => 'jours',
    'LBL_UNIT_MONTH' => 'mois',
    'LBL_UNIT_MONTHS' => 'mois',
    'LBL_UNIT_WEEK' => 'semaine',
    'LBL_UNIT_WEEKS' => 'semaines',
    'LBL_UNIT_YEAR' => 'année',
    'LBL_UNIT_YEARS' => 'années',
    'LBL_UNIT_YESTERDAY' => 'hier',
    'LBL_UNIT_TOMORROW' => 'demain',
    
    'LBL_BLOG' => 'Blogue',
    'LBL_QUESTION' => 'Question',
    'LBL_FILE' => 'Fichier',
    'LBL_BOOKMARK' => 'Signet',
    'LBL_UPDATE' => 'Mise à jour',
    'LBL_ACTIVITY' => 'Activité',
    'LBL_TITLE' => 'Titre',
    'LBL_CONTENT' => 'Contenu',
    'LBL_COMMENT_BY' => 'commenté par',
    'LBL_COMMENT' => 'Commentaire',
    'LBL_DUE' => 'Terminé le',
    'LBL_LIKE' => 'Recommendation',
    'LBL_ADD_TODO' => 'Ajouter une tâche',
    'LBL_ADD_ENTRY' => 'Ajouter une entrée',
    'LBL_ADD_SECTION' => 'Ajouter une section',
    'LBL_VIEW' => 'Voir',
    'LBL_SAVE' => 'Sauvegarder',
    'LBL_CANCEL' => 'Annuler',
    'LBL_CLOSE' => 'Fermer',
    'LBL_ACTIONS' => 'Actions',
    'LBL_BY_U' => 'Par',
    'LBL_REPLY' => 'Répondre',
    'LBL_REPLIES' => 'réponses',
    'LBL_LAST_POST_BY' => 'Dernier ajout par',
    'LBL_PEOPLE' => 'personnes',
    'LBL_PERSON' => 'personne',
    'LBL_DUE_DATE' => 'Date de fin',
    'LBL_ACTIVITY_GOAL' => 'Objectif de l&#39;activité',
    'LBL_ASSIGN' => 'Assigné à',
    'LBL_SELECT_FILE' => 'Choisir un fichier',
    'LBL_WIDGET_IS_NOT_ACTIVATED' => 'Cette application n&#39;est pas activé pour cette Communauté, veuillez l&#39,activé dans IBM Connections puis rafraichir cette page.',
    'LBL_DROP_FILE_HERE' => 'Déposer un fichier ici',
    'LBL_ASSIGNED_TO' => 'Assigné à',
);
