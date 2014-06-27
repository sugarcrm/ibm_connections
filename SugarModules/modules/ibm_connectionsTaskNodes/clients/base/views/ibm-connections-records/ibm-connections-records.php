<?php


$viewdefs['ibm_connectionsTaskNodes']['base']['view']['ibm-connections-records'] = array(

    'todoCh' => array(
        array('type' => 'bool', 'name' => "completed")
    ),

    'row_actions' => array(
            array (
                'type' => 'rowaction',
                'event' => 'button:delete_button:click',
                'icon' => 'icon-remove-circle',
                'css_class' => 'btn btn-mini',
                'target' => 'view',
//                'name' => 'delete_button',
//                'label' => 'LBL_DELETE_BUTTON_LABEL',                
//                'showOn' => 'edit',
//                'acl_action' => 'edit',
            ),


        /*
         * 
         *         array (
          'type' => 'rowaction',
          'event' => 'button:save_button:click',
          'name' => 'save_button',
          'label' => 'LBL_SAVE_BUTTON_LABEL',
          'css_class' => 'btn btn-primary',
          'showOn' => 'edit',
          'acl_action' => 'edit',
        ),
        
         * 
         * array(
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
);
