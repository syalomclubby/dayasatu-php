<?php
include "../config/connection.php"
?>

<section>
    <form action="check_login.php" method="post">
        <input type="text" name="name" id="name" placeholder="Username">
        <input type="password" name="password" id="password" placeholder="password">
        <button type="submit">Login</button>
    </form>
</section> 