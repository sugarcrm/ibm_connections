<div class="list" style="overflow: auto; height: 400px;">
    <table cellpadding="0" cellspacing="0" width="100%" border="0" class="list view">
        <tbody>
        <tr height="20">
            <th scope="col" width="10%" nowrap="nowrap">
                <div style="white-space: normal; padding: 5px;" width="100%" align="left">
                    Logo
                </div>
            </th>

            <th scope="col" width="70%" nowrap="nowrap">
                <div style="white-space: normal;" width="100%" align="left">
                    Name
                </div>
            </th>

            <th scope="col" width="20%" nowrap="nowrap">
                <div style="white-space: normal;" width="100%" align="left">
                    Type
                </div>
            </th>
        </tr>

        {foreach from=$communities item=i}
            <tr height="20" class="oddListRowS1">
                <td scope="row" align="left" valign="top" class="oddListRowS1" bgcolor="">
                    <img src="{$i.logo}" style="width: 30px; height: 20px;" />
                </td>

                <td scope="row" align="left" valign="top" class="oddListRowS1" bgcolor="">
                    <a onclick="selectCommunity()" data-id = "{$i.id}" style="cursor: pointer">
                        {$i.name}
                    </a>
                </td>

                <td scope="row" align="left" valign="top" class="oddListRowS1" bgcolor="">
                    {$i.type}
                </td>

            </tr>
        {/foreach}
        </tbody>
    </table>
</div>