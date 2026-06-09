<?php
include "../config/koneksi.php"
?>

<section>
    <form action="cek_login.php" method="post">
        <input type="text" name="nama" id="nama" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="password">
        <button type="submit">Login</button>
    </form>
</section> 