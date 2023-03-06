<?php
require_once 'inc/modules_config.php';
if (!($modules['module_visits'])) {
	header('Location: /404');
	exit;
}
include_once 'app/appPagination.php';
require_once 'modules/db.php';

$baseURL = 'actions.php';
$limit = 10;

$STM2 = $pdo2->query("SELECT connect FROM SP_users_visits ORDER BY connect LIMIT 1");
$row = $STM2->fetch();
$dMin = date("m/d/Y", $row[0]);
$dStart = $row[0];

$STM2 = $pdo2->query("SELECT connect FROM SP_users_visits ORDER BY connect DESC LIMIT 1");
$row = $STM2->fetch();
$dMax = date("m/d/Y", $row[0] + 86400);
$dEnd = $row[0];

if(($dEnd - 604800) > $dStart) {
	$dStart = $dEnd - 604800;
}
$dStartTime = $dStart;
$dStart = date("m/d/Y", $dStart);

$STM2GT = $pdo2->query("SELECT connect, players, now, disconnect FROM SP_users_visits WHERE connect > $dStartTime ORDER BY connect ASC");
$GT = array();
while ($row = $STM2GT->fetch()) {
	$GT[date("m/d/Y", $row[0])][1]++;
	if ($row[2] == 1) {
		$GT[date("m/d/Y", $row[0])][2]++;
	}
	$GT[date("m/d/Y", $row[0])][3] += $row[1];
	if ($row[3] > 0) {
		$GT[date("m/d/Y", $row[0])][4] += ($row[3] - $row[0]) / 60 / 60;
	}
}


$STM2 = $pdo2->query("SELECT COUNT(*) FROM SP_users_visits WHERE connect > $dStartTime");
$row = $STM2->fetch();
$rowCount = $row[0];

$visits_time = 0;
$STM2 = $pdo2->query("SELECT COUNT(*) FROM SP_users_visits WHERE connect > $dStartTime AND now = 1");
$s_newplayers = $STM2->fetch();
$STM2 = $pdo2->query("SELECT COUNT(*) FROM SP_users_visits WHERE connect > $dStartTime");
$s_ollvisits = $STM2->fetch();
$STM2 = $pdo2->query("SELECT connect, disconnect FROM SP_users_visits WHERE connect > $dStartTime");
while ($row = $STM2->fetch()) {
	if ($row[1] > 0) {
		$visits_time = $visits_time + ($row[1] - $row[0]) / 60 / 60;
	}
}

$srv = array();
$STM = $pdo->query("SELECT server_id, server_shortname FROM syspanel_servers ORDER BY server_id");
while ($row = $STM->fetch()) {
	$srv[$row[0]] = $row[1];
}

$pagConfig = array(
	'baseURL' => $baseURL,
	'totalRows' => $rowCount,
	'perPage' => $limit,
	'contentDiv' => 'dataResult',
	'link_func' => 'searchFilter'
);

$pagination = new Pagination($pagConfig);

$STM2 = $pdo2->query("SELECT SP_users_visits.server, SP_users_visits.connect, SP_users_visits.ip, SP_users_visits.geo, SP_users.nick, SP_users.steamid64, SP_users_visits.disconnect FROM SP_users_visits JOIN SP_users ON SP_users.id = SP_users_visits.uid WHERE connect > $dStartTime ORDER BY connect DESC LIMIT $limit");
?>

<link rel="stylesheet" href="modules/visits/template/styles/styles.css">
<script type="text/javascript" src="modules/visits/ajax/ajax.js"></script>
<script type="text/javascript" src="modules/visits/template/scripts/chart.min.js"></script>

<script type="text/javascript" src="modules/visits/template/scripts/moment.min.js"></script>
<script type="text/javascript" src="modules/visits/template/scripts/daterangepicker.js"></script>

<main>
    <div class='nav-list'>
        <nav>
            <div class="form">
                <input class='inp-ul' type="text" placeholder="<?= $language[$lang]["inputchat"] ?>" id="search">
                <select id="type_srv" onchange="searchFilter();">
                    <option value=""><?= $language[$lang]["allservers"] ?></option>
					<?php
					$STM = $pdo->query("SELECT server_name, server_id FROM syspanel_servers");
					while ($row = $STM->fetch()) {
						?>
                        <option value='<?= $row[1] ?>'><?= $row[0] ?></option>
					<?php } ?>
                </select>
                <select id='type_id' onchange="searchFilter();">
                    <option value="connect"><?=$language[$lang]["conn"]?></option>
                </select>
                <input id="reportrange" class='inp-ul' readonly>

                <a href='/' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]["back"]?></a>
            </div>
        </nav>

        <nav>
            <span class='t-3'><?=$language[$lang]["newplayers"]?></span>
            <p class='t-size-xl t-2' id='newplayers'><?= $s_newplayers[0] ?></p>
        </nav>

        <nav>
            <span class='t-3'><?=$language[$lang]["connserver"]?></span>
            <p class='t-size-xl t-2' id='ollvisits'><?= $s_ollvisits[0] ?></p>
        </nav>

        <nav>
            <span class='t-3'><?=$language[$lang]["timeplay"]?></span>
            <p class='t-size-xl t-2' id='visits_time'><?= round($visits_time) ?> <?=$language[$lang]["hours"]?></p>
        </nav>

    </div>
    <section>
        <div class='charts'>
            <div class='chart'>
                <canvas id="chart_1"></canvas>
            </div>
            <div class='chart'>
                <canvas id="chart_2"></canvas>
            </div>
            <div class='chart'>
                <canvas id="chart_3"></canvas>
            </div>
        </div>
        <div class='section-table'>
            <!--
			С прелоадом чёт не красиво...
			<div class="three-balls">
			  <div class="ball ball1"></div>
			  <div class="ball ball2"></div>
			  <div class="ball ball3"></div>
			</div>
		  -->

            <div id='dataResult'>
                <table>
                    <thead>
                    <tr>
                        <th></th>
                        <th><?=$language[$lang]["date"]?></th>
                        <th><?=$language[$lang]["country"]?></th>
                        <th><?=$language[$lang]["srv"]?></th>
                        <th><?=$language[$lang]["nick"]?></th>
                        <th><?=$language[$lang]["action"]?></th>
                        <th>IP</th>
                        <th><?=$language[$lang]["session"]?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					while ($row = $STM2->fetch()) {
						?>

                        <tr>
                            <td><a href='profile?id=<?= $row[5] ?>'><img
                                            src='modules/visits/template/images/icon_user_profile.svg'></a></td>
                            <td><?= date("d.m", $row[1]); ?><span
                                        class='t-xl t-size-xl'> <?= date("H:i", $row[1]); ?></span></td>
                            <td><?= (empty($row[3]) ? '-' : $row[3]) ?></td>
                            <td><?= $srv[$row[0]] ?></td>
                            <td><?= strip_tags($row[4]); ?></td>
                            <td><?=$language[$lang]["connected"]?></td>
                            <td><?= $row[2] ?></td>
                            <td><?= (($row[6] > 0) ? round(($row[6] - $row[1]) / 60) . ' '.$language[$lang]["minutes"] : '-') ?></td>
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
        </div>

    </section>
</main>

<script type="text/javascript">

    var start, end;
    let src2;

    $(function () {

        start = '<?php echo $dStart?>';
        end = '<?php echo $dMax?>';

        const langdow = <?= json_encode( $language[$lang]["daysofweek"] ) ?>,
            langmonth = <?= json_encode( $language[$lang]["monthNames"] ) ?>;

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

    let gt = <?php echo json_encode($GT) ?>;
    let arr1 = [], arr2 = [], arr3 = [], arr4 = [], arr5 = [];
    for (let key in gt) {
        arr1.push(key.slice(0, -5));
        arr2.push(gt[key][1]);
        arr3.push(gt[key][2]);
        arr4.push(Math.round(gt[key][3] / gt[key][1]));
        arr5.push(Math.round(gt[key][4]));
    }

    const data_1 = {
        labels: arr1,
        datasets: [{
            type: 'line',
            label: <?=json_encode($language[$lang]["chartjoins"])?>,
            data: arr2,
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.3
        }, {
            type: 'line',
            label: <?=json_encode($language[$lang]["chartnewplayers"])?>,
            data: arr3,
            fill: true,
            tension: 0.3,
            borderColor: 'rgb(54, 162, 235)'
        }]
    };

    const config_1 = {
        type: 'scatter',
        data: data_1,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const myChart_1 = new Chart(
        document.getElementById('chart_1'),
        config_1
    );

    //-------------------------------------------------

    const data_2 = {
        labels: arr1,
        datasets: [{
            type: 'line',
            label: <?=json_encode($language[$lang]["chartonline"])?>,
            data: arr4,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            tension: 0.3
        }]
    };

    const config_2 = {
        type: 'scatter',
        data: data_2,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const myChart_2 = new Chart(
        document.getElementById('chart_2'),
        config_2
    );

    //-------------------------------------

    const data_3 = {
        labels: arr1,
        datasets: [{
            type: 'line',
            label: <?=json_encode($language[$lang]["charthours"])?>,
            data: arr5,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            tension: 0.3
        }]
    };

    const config_3 = {
        type: 'scatter',
        data: data_3,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const myChart_3 = new Chart(
        document.getElementById('chart_3'),
        config_3
    );

    let doc = document.getElementById('search');
    doc.addEventListener('keyup', function (event) {
        if (event.which === 13 && doc.value && doc.value !== src2) {
            event.preventDefault();
            src2 = doc.value;
            searchFilter();
        }
    });
</script>