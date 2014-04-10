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
                    array(
                        'type' => 'dashletaction',
                        'action' => 'addItem',
                        'params' => array(
                            'module' => 'ibm_connectionsTasks',
                        ),
                        'label' => 'Add Activity',
//                        'acl_action' => 'create',
//                        'acl_module' => 'Emails',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'addItem',
                        'params' => array(
                            'module' => 'ibm_connectionsFiles',
                        ),
                        'label' => 'Add File',
//                        'acl_action' => 'create',
//                        'acl_module' => 'Emails',
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
    /*'custom_toolbar' => array(
        'buttons' => array(
            array(
                'type' => 'actiondropdown',
                'no_default_action' => true,
                'icon' => 'icon-plus',
                'buttons' => array(
                    array(
                        'type' => 'dashletaction',
                        'action' => 'archiveEmail',
                        'params' => array(
                            'link' => 'emails',
                            'module' => 'Emails',
                        ),
                        'label' => 'LBL_ARCHIVE_EMAIL',
                        'acl_action' => 'create',
                        'acl_module' => 'Emails',
                    ),
                ),
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
    ),*/
    'panels' => array(
        array(
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array(
                array(
                    'name' => 'community_id',
                    'label' => 'Select a community',
                    'type' => 'enum',
                    'options' => array('' => ''),
                ),
            ),
        ),
    ),
    'filter' => array(
        /*array(
            'name' => 'filter',
            'label' => 'LBL_FILTER',
            'type' => 'enum',
            'options' => 'history_filter_options'
        ),*/
    ),
    'tabs' => array(
        array(
            'active' => false,
//            'filter_applied_to' => 'date_start',
            'filters' => array(
//                'status' => array('$equals' => 'Held'),
            ),
            'labels' => array(
                'singular' => 'LBL_HISTORY_DASHLET_EMAIL_SINGULAR',
                'plural' => 'Team',
            ),
//            'link' => 'meetings',
            'module' => 'ibm_connectionsMembers',
//            'order_by' => 'date_start:desc',
//            'record_date' => 'date_start',
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
                            'label' => 'New To do',
                        ),
                        array(
                            'type' => 'dashletaction',
//                            'event' => 'button:show_members_tasks:click',
                            'name' => 'edit_button',
                            'label' => 'View Tasks',
                            'action' => 'showMemberTasks',
                        ),
                        array(
                            'type' => 'dashletaction',
//                            'event' => 'button:show:member_profile:click',
                            'name' => 'edit_button',
                            'label' => 'Profile',
                            'action' => 'showMemberProfile',
                        ),

                        array(
                            'type' => 'dashletaction',
//                            'event' => 'button:unlink:member2community:click',
                            'name' => 'edit_button',
                            'label' => 'Remove',
                            'action' => 'rmLink',
                            'params' => array(
                                'module' => 'ibm_connectionsMembers',
                                'link' => 'community_member',
                            ),
                        ),

                    ),
                ),


                /*array(
                    'type' => 'rowaction',
                    'icon' => 'icon-remove-circle',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
                   // 'acl_action' => 'edit',
                ),*/
                /*  array(
                      'type' => 'unlink-action',
                      'icon' => 'icon-unlink',
                      'css_class' => 'btn btn-mini',
                      'event' => 'tabbed-dashlet:unlink-record:fire',
                      'target' => 'view',
                      'tooltip' => 'LBL_UNLINK_BUTTON',
                   //   'acl_action' => 'edit',
                  ), */



            ),
            'include_child_items' => true,
        ),
        array(
            'active' => true,
//            'filter_applied_to' => 'date_start',
            'filters' => array(
//                'status' => array('$equals' => 'Held'),
            ),
            'labels' => array(
                'singular' => 'Task',
                'plural' => 'Tasks',
            ),
//            'link' => 'meetings',
            'module' => 'ibm_connectionsTasks',
//            'order_by' => 'date_start:desc',
//            'record_date' => 'date_start',
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
                            'label' => 'New To do',
                        ),
                        array(
                            'type' => 'dashletaction',
                            'action' => 'addItem',
                            'params' => array(
                                'module' => 'ibm_connectionsEntries',
//                                'link' => 'member_task',
                                'fieldMap' => array(
                                    'task_id' => 'id'
                                )
                            ),
                            'name' => 'edit_button',
                            'label' => 'New Entry',
                        ),
                        array(
                            'type' => 'dashletaction',
//                            'event' => 'button:unlink:member2community:click',
                            'name' => 'edit_button',
                            'label' => 'Remove',
                            'action' => 'rmLink',
                            'params' => array(
                                'module' => 'ibm_connectionsMembers',
                                'link' => 'community_member',
                            ),
                        ),

                    ),
                ),

                /*array(
                    'type' => 'rowaction',
                    'icon' => 'icon-remove-circle',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
                   // 'acl_action' => 'edit',
                ),*/
                /*  array(
                      'type' => 'unlink-action',
                      'icon' => 'icon-unlink',
                      'css_class' => 'btn btn-mini',
                      'event' => 'tabbed-dashlet:unlink-record:fire',
                      'target' => 'view',
                      'tooltip' => 'LBL_UNLINK_BUTTON',
                   //   'acl_action' => 'edit',
                  ), */



            ),
            'include_child_items' => true,
        ),
        array(
            'active' => false,
//            'filter_applied_to' => 'date_start',
            'filters' => array(
//                'status' => array('$equals' => 'Held'),
            ),
            'labels' => array(
                'singular' => 'File',
                'plural' => 'Files',
            ),
//            'link' => 'meetings',
            'module' => 'ibm_connectionsFiles',
//            'order_by' => 'date_start:desc',
//            'record_date' => 'date_start',
            'row_actions' => array(
                array(
                    'type' => 'dashletaction',
//                  'event' => 'button:unlink:member2community:click',
//                  'name' => 'edit_button',
                    'icon' => 'icon-remove-circle',
                    'css_class' => 'btn btn-mini',
                    'action' => 'rmLink',
                    'params' => array(
                        'module' => 'ibm_connectionsFiles',
                        'link' => 'community_files',
                    ),
                ),

                /*array(
                    'type' => 'rowaction',
                    'icon' => 'icon-remove-circle',
                    'css_class' => 'btn btn-mini',
                    'event' => 'active-tasks:close-task:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_ACTIVE_TASKS_DASHLET_COMPLETE_TASK',
                   // 'acl_action' => 'edit',
                ),*/
                /*  array(
                      'type' => 'unlink-action',
                      'icon' => 'icon-unlink',
                      'css_class' => 'btn btn-mini',
                      'event' => 'tabbed-dashlet:unlink-record:fire',
                      'target' => 'view',
                      'tooltip' => 'LBL_UNLINK_BUTTON',
                   //   'acl_action' => 'edit',
                  ), */



            ),
            'include_child_items' => true,
        ),



        /*array(
            'filter_applied_to' => 'date_entered',
            'filters' => array(
                'type' => array('$in' => array('out', 'inbound', 'archived')),
            ),
            'labels' => array(
                'singular' => 'LBL_HISTORY_DASHLET_EMAIL_SINGULAR',
                'plural' => 'Tasks',
            ),
            'link' => 'archived_emails',
            'module' => 'Emails',
            'order_by' => 'date_entered:desc',
            'row_actions' => array(
                array(
                    'type' => 'unlink-action',
                    'icon' => 'icon-unlink',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ),
            ),
        ),
        array(
            'filter_applied_to' => 'date_start',
            'filters' => array(
                'status' => array('$equals' => 'Held'),
            ),
            'labels' => array(
                'singular' => 'LBL_HISTORY_DASHLET_EMAIL_SINGULAR',
                'plural' => 'Files',
            ),
            'link' => 'calls',
            'module' => 'Calls',
            'order_by' => 'date_start:desc',
            'record_date' => 'date_start',
            'row_actions' => array(
                array(
                    'type' => 'unlink-action',
                    'icon' => 'icon-unlink',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ),
            ),
            'include_child_items' => true,
        ), */
    ),
    'visibility_labels' => array(
        'user' => 'LBL_HISTORY_DASHLET_USER_BUTTON_LABEL',
        'group' => 'LBL_HISTORY_DASHLET_GROUP_BUTTON_LABEL',
    ),
);
