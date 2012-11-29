<link rel="stylesheet" type="text/css" href="custom/modules/Connectors/connectors/sources/ext/eapm/connections/tpls/ProfileCard.css" />

<div id="memberProfile">

    <table border="0" cellpadding="0" cellspacing="0" class="profileTabs">
        <tr>
            <td><a href="{$profile.member_blogs}" target="_blank">{$language.LBL_MEMBER_BLOGS}</a></td>
            <td><a href="{$profile.member_forums}" target="_blank">{$language.LBL_MEMBER_FORUMS}</a></td>
            <td><a href="{$profile.member_wikis}" target="_blank">{$language.LBL_MEMBER_WIKIS}</a></td>
            <td><a href="{$profile.member_files}" target="_blank">{$language.LBL_MEMBER_FILES}</a></td>
            <td><a href="{$profile.member_communities}" target="_blank">{$language.LBL_MEMBER_COMMUNITIES}</a></td>
            <td><a href="{$profile.member_bookmarks}" target="_blank">{$language.LBL_MEMBER_BOOKMARKS}</a></td>
            <td><a href="{$profile.member_profiles}" target="_blank">{$language.LBL_MEMBER_PROFILE}</a></td>
            <td><a href="{$profile.member_activities}" target="_blank">{$language.LBL_MEMBER_ACTIVITIES}</a></td>
        </tr>
    </table>
    <br/>
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="75"><img src="{$profile_image_url}" width="75" /></td>
            <td width="25">&nbsp;</td>
            <td width="300" valign="top">
                <span class="memberName">{$profile.member_name}</span><br/>
                <span>{$profile.member_title}</span><br/>
                <span>{$member_address}</span><br/>
            </td>
        </tr>
    </table>
    <pre>{$profile.rawResponse}</pre>

</div>