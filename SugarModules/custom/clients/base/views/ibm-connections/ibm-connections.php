<?php

/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ('Company') that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

$viewdefs['base']['view']['ibm-connections'] = array(
    'dashlets' => array(
        array(
            'label' => 'LBL_IBM-CONNECTIONS_DASHLET',
            'description' => 'LBL_IBM-CONNECTIONS_DASHLET_DESCRIPTION',
            'config' => array(
                'limit' => '10',
                'filter' => '7',
                'visibility' => 'user',
            ),
            'preview' => array(
                'limit' => '10',
                'filter' => '7',
                'visibility' => 'user',
            ),
            'filter' => array(
                'module' => array(
                    'Accounts',
                    'Bugs',
                    'Cases',
                    'Contacts',
                    'Home',
                    'Leads',
                    'Opportunities',
                    'Prospects',
                    'RevenueLineItems',
                ),
                'view' => 'record',
            ),
        ),
    ),
    'custom_toolbar' => array(
        'buttons' => array(
            array(
                'type' => 'actiondropdown',
                'no_default_action' => true,
                'icon' => 'icon-plus',
                'buttons' => array(
                    /*array(
                        'type' => 'dashletaction',
                        'action' => 'rk',
                        'params' => array(
                            'module' => 'ibm_connectionsTasks',
                        ),
                        'label' => 'rk',
                    ),*/
                    array(
                        'type' => 'dashletaction',
                        'action' => 'addItem',
                        'params' => array(
                            'module' => 'ibm_connectionsTasks',
                        ),
                        'label' => 'LBL_IBM-CONNECTIONS_ADD_ACTIVITY',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'addItem',
                        'params' => array(
                            'module' => 'ibm_connectionsFiles',
                        ),
                        'label' => 'LBL_IBM-CONNECTIONS_ADD_FILE',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'addItem',
                        'params' => array(
                            'module' => 'ibm_connectionsMembers',
                        ),
                        'label' => 'LBL_IBM-CONNECTIONS_ADD_MEMBER',
                    ),
                )
            ),
            array(
                'dropdown_buttons' => array(
                    array(
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'toggleClicked',
                        'label' => 'LBL_DASHLET_MINIMIZE',
                        'event' => 'minimize',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ),
                ),
            ),
        ),
    ),
    'panels' => array(
        array(
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array(
                array(
                    'name' => 'community_id',
                    'label' => 'LBL_IBM-CONNECTIONS_SELECT_COMMUNITY',
                    'type' => 'enum',
                    'options' => array('' => ''),
                ),
            ),
        ),
    ),
    'filter' => array( ),
    'tabs' => array(
        array(
            'active' => true,
            'filters' => array( ),
            'labels' => array(
                'singular' => 'LBL_IBM-CONNECTIONS_TAB_MEMBER_SINGULAR',
                'plural' => 'LBL_IBM-CONNECTIONS_TAB_MEMBER_PLURAL',
            ),
            'module' => 'ibm_connectionsMembers',
            'row_actions' => array(
                array(
                    'type' => 'actiondropdown',
                    'buttons' => array(
                        array(
                            'type' => 'dashletaction',
                            'action' => 'addItem',
                            'params' => array(
                                'module' => 'ibm_connectionsTodos',
                                'fieldMap' => array(
                                    'assigned_user_id' => 'id'
                                )
                            ),
                            'name' => 'edit_button',
                            'label' => 'LBL_IBM-CONNECTIONS_BTN_ADD_TODO',
                        ),
                        /*
                                                array(
                                                    'type' => 'dashletaction',
                                                    'name' => 'edit_button',
                                                    'label' => 'View Tasks',
                                                    'action' => 'showMemberTasks',
                                                ),
                                                array(
                                                    'type' => 'dashletaction',
                                                    'name' => 'edit_button',
                                                    'label' => 'Profile',
                                                    'action' => 'showMemberProfile',
                                                ),
                        */
                        array(
                            'type' => 'dashletaction',
                            'name' => 'edit_button',
                            'label' => 'LBL_IBM-CONNECTIONS_BTN_REMOVE',
                            'action' => 'unlinkRow',
                            'params' => array(
                                'link' => 'community_member',
                                'rhs_key' => 'community_id',
                            ),
                        ),
                    ),
                ),
            ),
            'include_child_items' => true,
        ),
        array(
            'active' => false,
            'filters' => array( ),
            'labels' => array(
                'singular' => 'LBL_IBM-CONNECTIONS_TAB_TASK_SINGULAR',
                'plural' => 'LBL_IBM-CONNECTIONS_TAB_TASK_PLURAL',
            ),
            'module' => 'ibm_connectionsTasks',
            'row_actions' => array(
                array(
                    'type' => 'actiondropdown',
                    'buttons' => array(
                        array(
                            'type' => 'dashletaction',
                            'action' => 'addItem',
                            'params' => array(
                                'module' => 'ibm_connectionsTodos',
                                'fieldMap' => array(
                                    'task_id' => 'id'
                                )
                            ),
                            'name' => 'edit_button',
                            'label' => 'LBL_IBM-CONNECTIONS_BTN_ADD_TODO',
                        ),
                        array(
                            'type' => 'dashletaction',
                            'action' => 'addItem',
                            'params' => array(
                                'module' => 'ibm_connectionsEntries',
                                'fieldMap' => array(
                                    'task_id' => 'id'
                                )
                            ),
                            'name' => 'edit_button',
                            'label' => 'LBL_IBM-CONNECTIONS_BTN_ADD_ENTRY',
                        ),
                        array(
                            'type' => 'dashletaction',
                            'name' => 'edit_button',
                            'label' => 'LBL_IBM-CONNECTIONS_BTN_REMOVE',
                            'action' => 'deleteRow',
                            'params' => array(
                                'module' => 'ibm_connectionsMembers',
                                'link' => 'community_member',
                            ),
                        ),

                    ),
                ),
            ),
            'include_child_items' => true,
        ),
        array(
            'active' => false,
            'filters' => array( ),
            'labels' => array(
                'singular' => 'LBL_IBM-CONNECTIONS_TAB_FILE_SINGULAR',
                'plural' => 'LBL_IBM-CONNECTIONS_TAB_FILE_PLURAL',
            ),
            'module' => 'ibm_connectionsFiles',
            'row_actions' => array(
                array(
                    'type' => 'dashletaction',
                    'icon' => 'icon-remove-circle',
                    'css_class' => 'btn btn-mini',
                    'action' => 'deleteRow',
                    'params' => array(
                        'module' => 'ibm_connectionsFiles',
                        'link' => 'community_files',
                    ),
                ),
            ),
            'include_child_items' => true,
        ),
    ),
    'visibility_labels' => array(
        'user' => 'LBL_HISTORY_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_HISTORY_DASHLET_GROUP_BUTTON_LABEL',
    ),
);
