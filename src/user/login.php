<?php
if (ESP::login()){
    ESP::redirect("/article/list");
}
?>
<form method="POST">
<p>user_id : <input type="text" name="login_id" id="login_id" value="<?= ESP::param("login_id") ?? "" ?>" /></p>
    <p>user_pw : <input type="password" name="login_pw" id="login_pw"  value="<?= ESP::param("login_pw") ?? "" ?>"/></p>
    <p><input type="submit" value="로그인" /></p>
</form>