<div class="login_wrap">
    <div id="login_note" class="fade"></div>
    <div class="login_controls">
        <input class="input_basic" type="text" id="code" placeholder="Code" autocomplete="off" autocorrect="off" spellcheck="false">
        <input type="hidden" id="phone" value="<?php echo self::$_tpl_vars['phone']; ?>
">
        <div class="btn_basic" onclick="common.auth_confirm();">Confirm</div>
    </div>
</div>