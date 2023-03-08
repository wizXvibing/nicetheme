<?php

function GetAvatar($arr, $steam64) {
	foreach($arr as $item) {
		if(strcmp($steam64, $item["steamid"]) == 0)
			return $item["avatarmedium"];
	}
	
	return 'modules/users/template/images/icon_user_profile.svg';
}

if (isset($_POST['page'])) {
	require_once '../../inc/modules_config.php';
	include_once '../../app/appPagination.php';
	require_once '../../modules/db.php';
	require_once '../../inc/db.php';
	require_once '../../app/appMessages.php';
	//include_once '../../app/components/steam.php';
	
	require '../../app/appSteam.php';
	require '../../inc/steam_config.php';
	$s = new steam($steam_api);

	$offset = !empty($_POST['page']) ? $_POST['page'] : 0;
	$search = $_POST['search'];

	$baseURL = 'actions.php';
	$offset = (!empty($_POST['page']) && is_numeric($_POST['page'])) ? $_POST['page'] : 0;
	$limit = 15;

	$STM2 = $pdo2->prepare("SELECT COUNT(*) FROM SP_users WHERE CONCAT(id,nick,steamid,steamid64,ip) LIKE ?");
	$STM2->execute(array("%" . $search . "%"));
	$rowCount = $STM2->fetch()[0];

	$pagConfig = array(
		'baseURL' => $baseURL,
		'totalRows' => $rowCount,
		'perPage' => $limit,
		'currentPage' => $offset,
		'contentDiv' => 'dataResult',
		'link_func' => 'searchFilter'
	);
	$pagination = new Pagination($pagConfig);

	//$STM2 = $pdo2->prepare("SELECT * FROM SP_users WHERE CONCAT(id,nick,steamid,steamid64,ip) LIKE ? ORDER BY id ASC LIMIT $offset,$limit");
	$STM2 = $pdo2->prepare("SELECT SP_users.id, SP_users.nick, SP_users.steamid, SP_users.steamid64, SP_users.ip, SP_users_activity.online FROM SP_users LEFT JOIN SP_users_activity ON SP_users.id=SP_users_activity.uid
		WHERE CONCAT(SP_users.id,SP_users.nick,SP_users.steamid,SP_users.steamid64,SP_users.ip) LIKE ? ORDER BY SP_users.id ASC LIMIT $offset,$limit");
	$STM2->execute(array("%" . $search . "%"));
	?>
    <!-- Data list container -->
    <div id='dataResult'>
        <table>
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th><?=$language[$lang]["nick"]?></th>
                <th>SteamID</th>
                <th>SteamID64</th>
                <th>IP</th>
            </tr>
            </thead>
            <tbody>
			<?php
			
			$data = $STM2->fetchAll();
			//var_dump($data);
			foreach($data as $item) {
				$steamids = $steamids . $item["steamid64"] . ",";
			}
			
			$steamids = substr($steamids, 0, -1);
			if ($steamids) $pArrayUsers = $s->getImageList($steamids);
			//var_dump($pArrayUsers);
			
			foreach($data as $item) {
				?>
                <tr>
					<td id='<?= $item["steamid64"] ?>'><a href='profile?id=<?= $item["steamid64"] ?>'>
						<div class="temp">
							<img src='<?= GetAvatar($pArrayUsers, $item["steamid64"]) ?>' class="img-circle-small">
							<span class="<?= $item["online"] ? 'status-green' : 'status-red' ?> bottomRight">&nbsp</span>
						</div>
					</a></td>
                    <td><?= $item["id"] ?></td>
                    <td><?= strip_tags($item["nick"]) ?></td>
                    <td><?= $item["steamid"] ?></td>
                    <td><?= $item["steamid64"] ?></td>
                    <td><?= $item["ip"] ?></td>
                </tr>
				<?php
			}

			if ($STM2->rowCount() == 0) {
				echo "<tr><td colspan='7' style='text-align:center;' class='t-4'>".$language[$lang]["notresult"]."</td></tr>";
			}
			?>
            </tbody>
        </table>
		<?php echo $pagination->createLinks(); ?>
    </div>
	<?php
}
