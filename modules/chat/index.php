<?php
require_once 'inc/modules_config.php';
if (!($modules['module_chat'])) {
	header('Location: /404');
	exit;
}
require_once 'modules/db.php';
require_once 'modules/chat/config.php';

$search = (isset($_GET['s']) && !empty($_GET['s'])) ? $_GET['s'] : "";

$srv = array();
$STM = $pdo->query("SELECT server_id, server_shortname FROM syspanel_servers ORDER BY server_id");
while ($row = $STM->fetch()) {
	$srv[$row[0]] = $row[1];
}

$STM2 = $pdo2->query("SELECT date FROM SP_users_chat ORDER BY date LIMIT 1");
$row = $STM2->fetch();
$dMin = date("m/d/Y", $row[0]);
$dStart = $row[0];
$time = $row[0];

$STM2 = $pdo2->query("SELECT date FROM SP_users_chat ORDER BY date DESC LIMIT 1");
$row = $STM2->fetch();
$dMax = date("m/d/Y", $row[0] + 86400);
$dEnd = $row[0];

if(($dEnd - 604800) > $dStart) {
	$dStart = $dEnd - 604800;
}

$dStart = date("m/d/Y", $dStart);

$STM = $pdo->query("SELECT server_name, server_id FROM syspanel_servers");

$steamids;

$STM2 = $pdo2->prepare("SELECT SP_users.nick AS nick, SP_users_chat.msg, SP_users_chat.date, SP_users.steamid64 AS steamid64, SP_users.steamid AS steamid, SP_users_chat.team, SP_users_chat.server, SP_users_chat.type, SP_users_chat.alive FROM SP_users_chat JOIN SP_users ON SP_users_chat.uid = SP_users.id WHERE CONCAT(nick, msg, steamid, steamid64) like ? " . $s_param . " ORDER BY date DESC LIMIT " . $chat_config['show'] . "");
$STM2->execute(array("%" . $search . "%"));
?>

<link rel="stylesheet" href="modules/chat/template/styles/styles.css">
<script type="text/javascript" src="modules/chat/ajax/ajax.js"></script>

<script type="text/javascript" src="modules/visits/template/scripts/moment.min.js"></script>
<script type="text/javascript" src="modules/visits/template/scripts/daterangepicker.js"></script>

<main>
    <nav>
        <div class="form">
            <input value="<?= $search ?>" class='inp-ul' type="text" placeholder="<?=$language[$lang]["inputchat"]?>" id="search">
            <select id="type_srv" onchange="searchFilter();">
                <option value=""><?=$language[$lang]["allservers"]?></option>
				<?php
				while ($row = $STM->fetch()) {
					?>
                    <option value='<?= $row[1] ?>'><?= $row[0] ?></option>
				<?php } ?>
            </select>
            <select id="type_id" onchange="searchFilter();">
                <option value=""><?=$language[$lang]["othertype"]?></option>
                <option value="say_team">say_team</option>
                <option value="sm_say">sm_say</option>
                <option value="sm_chat">sm_chat</option>
                <option value="sm_csay">sm_csay</option>
                <option value="sm_tsay">sm_tsay</option>
                <option value="sm_msay">sm_msay</option>
                <option value="sm_hsay">sm_hsay</option>
                <option value="sm_psay">sm_psay</option>
                <option value="say">say</option>
            </select>
            <select id="team_id" onchange="searchFilter();">
                <option value=""><?=$language[$lang]["otherteam"]?></option>
                <option value="1"><?=$language[$lang]["spec"]?></option>
                <option value="2"><?=$language[$lang]["ter"]?></option>
                <option value="3"><?=$language[$lang]["cter"]?></option>
            </select>
            <select id="alive_id" onchange="searchFilter();">
                <option value=""><?=$language[$lang]["otherstatus"]?></option>
                <option value="1"><?=$language[$lang]["alive"]?></option>
                <option value="2"><?=$language[$lang]["dead"]?></option>
            </select>
            <input id="reportrange" class='inp-ul' readonly>
            <a href='/' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]["back"]?></a>
        </div>
    </nav>
    <section class="result">
        <div id='dataResult'>
			<?php
			while ($row = $STM2->fetch()) {
				$borderteam__color = 'border__color' . $row[5];
				$alive__status = $row[8] == 1 ? "alive" : "dead";
				$steamids = $steamids . $row[3] . ","
				?>

                <div class='message'>
                    <div class='title'>
                        <img class="<?= $borderteam__color ?>" src='modules/chat/template/images/icon_chat_user.svg'>
                        <p class='t-3 t-size-sm t-md'><?= date('H:i', $row[2]); ?></p>
                    </div>
                    <div class='m-show'>
                        <a href='/profile?id=<?= $row[3] ?>'>
                            <span class='t-2 t-size-md nick'><?= strip_tags($row[0]) ?></span>
                        </a>
                        <span class='t-3 t-size-sm t-md'><?= date('d.m.y', $row[2]); ?></span>
                        <span class='t-3 t-size-sm t-md banner__banner'><?= $srv[$row[6]] . '/' . $row[7] . '/' . $alive__status ?></span>
                        <p class='text-message t-size-md t-3'><?= strip_tags($row[1]) ?></p>
                    </div>
                </div>

			<?php }
			$steamids = substr($steamids, 0, -1);
			if ($steamids && $chat_config['avatars']) $pArrayUsers = $s->getImageList($steamids);
			?>
        </div>
    </section>
</main>

<script>
    var start, end, search;
    let src2;

    const langdow = <?= json_encode( $language[$lang]["daysofweek"] ) ?>,
    langmonth = <?= json_encode( $language[$lang]["monthNames"] ) ?>;

    $(function () {
        start = '<?php echo $dStart?>';
        end = '<?php echo $dMax?>';

        function cb(start, end) {
            $('#reportrange').html(start + ' - ' + end);
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            minDate: '<?php echo $dMin?>',
            maxDate: '<?php echo $dMax?>',
            locale: {
                daysOfWeek: langdow,
                monthNames: langmonth,
                customRangeLabel: <?= json_encode( $language[$lang]["customRangeLabel"] ) ?>,
            },
            ranges: {
                <?= json_encode( $language[$lang]["today"] ) ?>: [moment(), moment()],
                <?= json_encode( $language[$lang]["yesterday"] ) ?>: [moment().subtract(1, 'days'), moment().subtract(0, 'days')],
                <?= json_encode( $language[$lang]["l7days"] ) ?>: [moment().subtract(6, 'days'), moment().add(1, 'days')],
                <?= json_encode( $language[$lang]["l30days"] ) ?>: [moment().subtract(29, 'days'), moment().add(1, 'days')],
                <?= json_encode( $language[$lang]["thismonth"] ) ?>: [moment().startOf('month'), moment().endOf('month')],
                <?= json_encode( $language[$lang]["lastmonth"] ) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('#reportrange').on('hide.daterangepicker', function (ev, picker) {
            start = picker.startDate.format('DD-MM-YYYY');
            end = picker.endDate.format('DD-MM-YYYY');
            searchFilter();
        });

    });

    let gravit = 0;
    let spisokid;
    window.onload = function () {
        let ar = <?php echo json_encode($pArrayUsers); ?>;
        if (ar) {
            let block = document.querySelectorAll('.message');

            block.forEach((el, idx) => {
                let sid = el.querySelector("a").getAttribute('href').slice(12);
                for (let i = 0; i < ar.length; i++) {
                    if (ar[i].steamid == sid) {
                        spisokid = i;
                        break;
                    }
                }
                el.querySelector('img').setAttribute('src', ar[spisokid]['avatarmedium'])
            });
        }
    }

    $(".result").on("scroll", scrolling);

    function scrolling() {
        var currentHeight = $(this).children("#dataResult").height();
        if ($(this).scrollTop() >= (currentHeight - $(this).height() - 100)) {
            $(this).unbind("scroll");
            NProgress.start();
            fireShow();
        }
    }

    function fireShow() {
        var search = $("#search").val();
        var type_id = document.getElementById("type_id").value;
        var team_id = document.getElementById("team_id").value;
        var alive_id = document.getElementById("alive_id").value;
        var type_srv = document.getElementById("type_srv").value;

        $.ajax({
            type: 'POST',
            url: 'modules/chat/actions.php',
            data: 'gravit=' + gravit + '&search=' + search + '&type_id=' + type_id + '&team_id=' + team_id + '&alive_id=' + alive_id + '&type_srv=' + type_srv + '&start=' + start + '&end=' + end,
            success: function (html) {
                $("#dataResult").append(html);
                $(".result").on("scroll", scrolling);
                NProgress.done();
            }
        });

        ++gravit;
        return false;
    }

    let doc = document.getElementById('search');
    doc.addEventListener('keyup', function (event) {
        if (event.which === 13 && doc.value && doc.value !== src2) {
            event.preventDefault();
            src2 = doc.value;
            searchFilter();
        }
    });
</script>