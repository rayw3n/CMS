<?php
// Site Informations
/**--**/
$meta['title'] = "User Verwalten";
//------------------------------------------------

if ($do == "") {
    switch ($action) {
        default:
            $users = db('SELECT id,name FROM users');
            $userlist = "";
            while ($user = _assoc($users)) {
                $userlist .= show('acp/acp_select_option',
                    array(
                        'value' => $user['id'],
                        'title' => $user['name']
                    ));
            }
            $disp = show('acp/acp_user',
                array(
                    'user_list' => $userlist
                ));
            break;
        case 'edit_group':
            $te = new TemplateEngine("acp/acp_groups_edit");
            $row['permission_list'] = "";
            $groups = Db::npquery('SELECT * FROM groups WHERE id = ' . $_GET['id'] . ' LIMIT 1');
            $case = array();
            foreach ($groups as $permission => $value) {
                if ($permission != 'id' && $permission != 'groupid') {
                    $case[] = array(
                        'permission_title' => '{s_' . strtoupper($permission) . '}',
                        'permission' => $permission,
                        'checked' => $value ? 'checked' : ''
                    );
                }
            }
            $te->addArr('permission_list', $case);
            $row['id'] = $_GET['id'];
            $row['group'] = $groups['groupid'];
            $disp = show($te->render(), $row);
            break;
        case 'group_list':
            $groups = db('SELECT id,groupid FROM groups');
            $rows = "";
            while ($group = _assoc($groups)) {
                $infos = "";
                foreach ($group as $value) {
                    $infos .= '<td>' . $value . "</td>";
                }
                $rows .= show('acp/acp_groups_tr',
                    array(
                        'infos' => $infos,
                        'id' => $group['id'],
                        'group_name' => $group['groupid']
                    ));
            }
            $disp = show('acp/acp_groups',
                array(
                    'rows' => $rows
                ));
            break;
    }
} else {
    switch ($do) {
        case 'update_settings':

            break;
        case 'update_group':
            $update = '';
            $groups = Db::npquery('SELECT * FROM groups WHERE id = ' . $id . ' LIMIT 1');
            foreach ($groups as $name => $value) {
                if ($name != 'id' && $name != 'groupid') {
                    $value = 0;
                    if (isset($_POST[$name]) && $_POST[$name] = 1) $value = 1;
                    $update .= ' ' . $name . ' = ' . $value . ',';
                }
            }
            $update = substr($update, 0, -1) . ' ';

            $disp = up('UPDATE groups SET ' . $update . 'WHERE id=' . $id) ? 'Update sucessful' : 'Update failed';
            break;
        case 'delete_group':
            if (permTo('delete_group')) {
                if (up('DELETE FROM groups WHERE id =' . $id)) {
                    $disp = msg(_change_successful);
                } else {
                    $disp = msg(_change_failed);
                }
            } else {
                $disp = msg(_no_permissions);
            }
            break;
    }
}
