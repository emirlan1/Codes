<div class="modal_head">
    <i class="icon_close" onclick="common.modal_hide()"></i>
</div>
<div class="modal_body">
    <div id="errorContainer" style="color: red"></div>
    <div class="input_group_modal">
        <div>First name</div>
        <input type="text" id="first_name" required value="<?php echo self::$_tpl_vars['user']['first_name']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Last name</div>
        <input type="text" id="last_name" required value="<?php echo self::$_tpl_vars['user']['last_name']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Phone</div>
        <input type="text" id="phone" required value="<?php echo self::$_tpl_vars['user']['phone']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Email</div>
        <input type="text" id="email" required value="<?php echo self::$_tpl_vars['user']['email']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Plots</div>
        <input type="text" id="plots" value="<?php echo self::$_tpl_vars['user']['plots']; ?>
">
    </div>
    <div class="modal_controls">
        <div>
            <div class="btn_modal" onclick="common.user_edit_update(<?php echo self::$_tpl_vars['user']['id']; ?>
);">Save</div>
        </div>
        <div>
            <div class="btn_modal light" onclick="common.modal_hide();">Cancel</div>
        </div>
    </div>
</div>