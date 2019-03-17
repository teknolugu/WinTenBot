<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/24/2019
 * Time: 10:21 AM
 */

define('PRESET_PROMOTE_MEMBERS', [
    'can_change_info' => false,
    'can_post_messages' => false,
    'can_edit_messages' => false,
    'can_delete_messages' => true,
    'can_invite_users' => true,
    'can_restrict_members' => true,
    'can_pin_messages' => true,
    'can_promote_members' => false,
]);

define('PRESET_DEMOTE_MEMBERS', [
    'can_change_info' => false,
    'can_post_messages' => false,
    'can_edit_messages' => false,
    'can_delete_messages' => false,
    'can_invite_users' => false,
    'can_restrict_members' => false,
    'can_pin_messages' => false,
    'can_promote_members' => false,
]);
