<?php

$role_list = array(
	$language[$lang]['role_0'],
	$language[$lang]['role_1'],
	$language[$lang]['role_2'],
	$language[$lang]['role_3'],
);

?>

<header>
    <a href='/'><img class="logo" src="public/images/logo.svg" alt="Logotype"></a>

    <div class="dropdown">
        <div class="profile">
            <div class="bt" style="text-align: end;">
                <p class="t-2"><?= $s->personaname ?></p>
                <p class="t-3 t-size-sm"><?= $role_list[$_SESSION['role']] ?></p>
            </div>
            <img src=<?= $_SESSION['profile_avatar'] ?> alt="">
            <img class="arrow" src="public/images/arrow.svg" alt="">
        </div>
        <div class="dropdown-content">
            <a href='profile?id=<?= $s->steamid; ?>'<span class="t-size-sm t-2"><?=$language[$lang]["myprofile"]?></span></a>
            <a onclick="toggleTheme()" id="switch"><span class="t-size-sm t-2"><?=$language[$lang]["changetheme"]?></span></a>
            <a href="?logout"><span class="t-size-sm t-2"><?=$language[$lang]["logout"]?></span></a>
        </div>
    </div>
</header>
