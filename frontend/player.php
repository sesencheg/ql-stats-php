7<?php
require_once('config.php');
if (isset($_GET["sid"])){
	$pl = mysqli_query($link, "SELECT *,SUM(KILLS) as sumk, SUM(DEATHS) as sumd, COUNT(*) as sumduels, SUM(LOSE) as losses, SUM(WIN) as wins, SUM(QUIT) as quits FROM stats WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."' GROUP BY STEAM_ID");
	$player = mysqli_fetch_array($pl);
	$de = mysqli_query($link, "SELECT * FROM players WHERE STEAM_ID='".$_GET[sid]."'");
	$elo = mysqli_fetch_array($de);
	$mat = mysqli_query($link, "SELECT MATCH_GUID FROM stats WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."'");
 	while($row = mysqli_fetch_array($mat)){
 		$match[] = "'$row[MATCH_GUID]'";
 	}	
 	$ids = join(',',$match); 
?>
<html xmlns="http://www.w3.org/1999/xhtml" >
	<head>
		<title>Duel</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen, projection" />
	    <script language="javascript" type="text/javascript" src="jquery.min.js"></script>
	    <script language="javascript" type="text/javascript" src="jquery.flot.min.js"></script>
	    <script language="javascript" type="text/javascript" src="jquery.flot.pie.min.js"></script>
	    <script language="javascript" type="text/javascript" src="jquery.cursorMessage.js"></script>
	    <script language="javascript" type="text/javascript" src="player.js"></script>
	    <script language="javascript" type="text/javascript" src="excanvas.min.js"></script>
	    <link rel="icon" href="../favicon.ico" type="image/x-icon" />
	    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	    <link href='https://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
    </head>
	<body>
	<div id="header">
        <div class="inner">
          <div id="logo">
            <a href="/qlstats/"></a>
          </div>
          <div id="tracking">
          	<?
          		$duels = mysqli_query($link, "SELECT * FROM stats, matches WHERE stats.WARMUP!='1' AND stats.MATCH_GUID = matches.MATCH_GUID AND matches.GAME_TYPE='DUEL' GROUP BY stats.MATCH_GUID");
          		$row = mysqli_num_rows($duels);

          		$players = mysqli_query($link, "SELECT * FROM players");
          		$pl = mysqli_num_rows($players);          		
          		
          	?>
      	    <strong><span id="ctl00_lblDuelers"><? echo $pl; ?></span></strong> Players 
      	    <strong><span id="ctl00_lblDuels"><? echo $row; ?></span></strong> Duels 

          </div> 
          <div class="clear"></div>
        </div>
    </div>
    <div id="content">
        <div class="inner">
        	<div id="player">
    			<div id="top">
    				<h1><? echo quakename($player[NAME]); ?></h1>
    				<div id="stats">ELO: <strong><span><? echo $elo[DUEL_ELO]; ?></span></strong> Kills: <strong><span><? echo $player[sumk]; ?></span></strong> Deaths: <strong><span><? echo $player[sumd]; ?></span></strong> Duels Tracked: <strong><span><? echo $player[sumduels]; ?></span></strong> KDR: <strong><span><? echo $player[sumk]-$player[sumd]; ?></span></strong></div>
    				<div class="line"></div>
    			</div>
				<div class="left-sixth">
            		<div id="avatar">
                		<img id="ctl00_ContentPlaceHolder1_imgModel" src="images/models/<? echo $player[MODEL];?>.png" style="border-width:0px;">
            		</div>
            		<div id="medals">
            			<?php
            			 $medexc=0;
            			 $medimp=0;
            			 $medhum=0;
						 $m = mysqli_query($link, "SELECT *,COUNT(*) as summed FROM medals WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."' GROUP BY MEDAL");
						 while($medal = mysqli_fetch_array($m)){
						 	if ($medal[MEDAL] == "EXCELLENT"){
						 		$medexc=$medal[summed];
						 	}
						 	if ($medal[MEDAL] == "IMPRESSIVE"){
						 		$medimp=$medal[summed];
						 	}						 	
						 	if ($medal[MEDAL] == "GAUNTLET"){
						 		$medhum=$medal[summed];
						 	}	
						 }
						?>
                		<span class="medal exc"><span id="ctl00_ContentPlaceHolder1_lblExcellent"><? echo $medexc; ?></span></span>
                		<span class="medal imp"><span id="ctl00_ContentPlaceHolder1_lblImpressives"><? echo $medimp; ?></span></span>
                		<span class="medal hum"><span id="ctl00_ContentPlaceHolder1_lblHum"><? echo $medhum; ?></span></span>
            		</div>
        		</div>
				<div class="left-fourtenth">
            		<div class="box" id="most-played">
                		<h2>Map Distribution</h2>
                		<div id="mapgraph" class="graph" style="padding: 0px; position: relative;"></div>
            		</div>
        		</div>     
				<div class="right-fourtenth">
					<div class="box" id="vital_stats">
						<h2>Vital Stats</h2>
						<table>
							<tbody>
								<tr>
									<td>Wins</td>
									<td class="value"><span id="ctl00_ContentPlaceHolder1_lblDuelWins"><? echo $player[wins] ?></span></td>
								</tr>
								<tr>
									<td>Losses / Quits</td>
									<td class="value"><span id="ctl00_ContentPlaceHolder1_lblVSLosses"><? echo $player[losses] ?></span>  / <span id="ctl00_ContentPlaceHolder1_lblDuelQuits"><? echo $player[quits] ?></span> </td>
								</tr>
								<tr>
									<td>Frags / Deaths</td>
									<td class="value"><span id="ctl00_ContentPlaceHolder1_lblFrags"><? echo $player[sumk]; ?></span> / <span id="ctl00_ContentPlaceHolder1_lblDeaths"><? echo $player[sumd]; ?></span></td>
								</tr>
								<tr>
									<?
										$dg=mysqli_query($link, "SELECT DAMAGE FROM stats WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."'");	
										$pl_d = 0;
										$pl_t = 0;
										while($dmggt=mysqli_fetch_array($dg)){
											$dtg = json_decode(str_replace("'", '"', $dmggt[DAMAGE]));					                        		
											$pl_t = $pl_t+$dtg->TAKEN;
											$pl_d = $pl_d+$dtg->DEALT;
										}
									?>
									<td>Dmg G / T</td>
									<td class="value"><span id="ctl00_ContentPlaceHolder1_lbldmgGiven"><? echo $pl_d; ?></span>  / <span id="ctl00_ContentPlaceHolder1_lbldmgTaken"><? echo $pl_t; ?></span></td>
								</tr>
								<tr>
									<td>Arena</td>
									<?
										$m=mysqli_query($link, "SELECT COUNT(*), MAP FROM matches WHERE MATCH_GUID in ($ids) GROUP BY MAP ORDER BY COUNT(*) DESC LIMIT 1");	
										$map = mysqli_fetch_array($m);
									?>												
									<td class="value"><span id="ctl00_ContentPlaceHolder1_lblFaveArena"><? echo $map[1]; ?></span></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="box">
					    	<h2>
					        	Medals
					        </h2>    
							<table cellspacing="0" border="0" id="ctl00_ContentPlaceHolder1_gridTop100" style="border-collapse:collapse;">
								<tbody>
									<tr>
									<?
										$m1=mysqli_query($link, "SELECT COUNT(*), MEDAL FROM (SELECT * FROM medals WHERE STEAM_ID = '".$_GET[sid]."') as mm GROUP BY MEDAL");	
										while($med1 = mysqli_fetch_array($m1)){
											?>
													<td style="text-align:center;"><img width="80%" src="images/medals/medal_<? echo mb_strtolower($med1[1]) ?>.png"></td>
											<?
										}
									?>	
									</tr>
									<tr>
									<?
										$m1=mysqli_query($link, "SELECT COUNT(*), MEDAL FROM (SELECT * FROM medals WHERE STEAM_ID = '".$_GET[sid]."') as mm GROUP BY MEDAL");	
										while($med1 = mysqli_fetch_array($m1)){
											?>
													<td style="text-align:center;"><? echo $med1[0]; ?></td>																									
											<?
										}
									?>	
									</tr>																											
								</tbody>
							</table>
						</div>					
		        </div>  
				<div class="box center" id="weapon_stats">
				
					<?
						$w=mysqli_query($link, "SELECT WEAPONS FROM stats WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."'");	
						$gaunt_s = 0;
						$gaunt_h = 0;
						$gaunt_k = 0;
						$mg_s = 0;
						$mg_h = 0;						
						$mg_k = 0;
						$sg_s = 0;
						$sg_h = 0;
						$sg_k = 0;
						$gl_s = 0;
						$gl_h = 0;							
						$gl_k = 0;							
						$rl_s = 0;
						$rl_h = 0;		
						$rl_k = 0;
						$lg_s = 0;
						$lg_h = 0;		
						$lg_k = 0;
						$rg_s = 0;
						$rg_h = 0;		
						$rg_k = 0;
						$pg_s = 0;
						$pg_h = 0;																										
						$pg_k = 0;																										
						while($weapons=mysqli_fetch_array($w)){
							$dtg = json_decode(str_replace("'", '"', $weapons[WEAPONS]));					                        		
							$gaunt_s = $gaunt_s+$dtg->GAUNTLET->S;
							$gaunt_h = $gaunt_h+$dtg->GAUNTLET->H;
							$gaunt_k = $gaunt_k+$dtg->GAUNTLET->K;
							$mg_s = $mg_s+$dtg->MACHINEGUN->S;
							$mg_h = $mg_h+$dtg->MACHINEGUN->H;
							$mg_k = $mg_k+$dtg->MACHINEGUN->K;
							$sg_s = $sg_s+$dtg->SHOTGUN->S;
							$sg_h = $sg_h+$dtg->SHOTGUN->H;
							$sg_k = $sg_k+$dtg->SHOTGUN->K;
							$gl_s = $gl_s+$dtg->GRENADE->S;
							$gl_h = $gl_h+$dtg->GRENADE->H;
							$gl_k = $gl_k+$dtg->GRENADE->K;
							$rl_s = $rl_s+$dtg->ROCKET->S;
							$rl_h = $rl_h+$dtg->ROCKET->H;
							$rl_k = $rl_k+$dtg->ROCKET->K;
							$lg_s = $lg_s+$dtg->LIGHTNING->S;
							$lg_h = $lg_h+$dtg->LIGHTNING->H;
							$lg_k = $lg_k+$dtg->LIGHTNING->K;
							$rg_s = $rg_s+$dtg->RAILGUN->S;
							$rg_h = $rg_h+$dtg->RAILGUN->H;
							$rg_k = $rg_k+$dtg->RAILGUN->K;
							$pg_s = $pg_s+$dtg->PLASMA->S;
							$pg_h = $pg_h+$dtg->PLASMA->H;
							$pg_k = $pg_k+$dtg->PLASMA->K;
						}
						$all_k=$gaunt_k+$mg_k+$sg_k+$gl_k+$rl_k+$lg_k+$rg_k+$pg_k;

					?>				            
					<h2>Weapon Stats</h2>
						<table>
							<thead>
								<tr>
									<th></th>
									<th><span class="icon gauntlet"></span></th>
									<th><span class="icon mg"></span></th>
									<th><span class="icon sg"></span></th>
									<th><span class="icon gl"></span></th>
									<th><span class="icon rl"></span></th>
									<th><span class="icon lg"></span></th>
									<th><span class="icon rg"></span></th>
									<th><span class="icon pg"></span></th>
								</tr>
							</thead>
							<tbody>
							<tr class="frags">
							<td class="title">Frags</td>
								<td><? echo $gaunt_k; ?></td>
								<td><? echo $mg_k; ?></td>
								<td><? echo $sg_k; ?></td>
								<td><? echo $gl_k; ?></td>
								<td><? echo $rl_k; ?></td>
								<td><? echo $lg_k; ?></td>
								<td><? echo $rg_k; ?></td>
								<td><? echo $pg_k; ?></td>
							</tr>
				            <tr class="accuracy">
				            	<td class="title">Accuracy</td>
				                <td>N/A</td>
				                <td><? echo round(($mg_h*100)/($mg_s),2); ?> %</td>
				                <td><? echo round(($sg_h*100)/($sg_s),2); ?> %</td>
				                <td><? echo round(($gl_h*100)/($gl_s),2); ?> %</td>
				                <td><? echo round(($rl_h*100)/($rl_s),2); ?> %</td>
				                <td><? echo round(($lg_h*100)/($lg_s),2); ?> %</td>
				                <td><? echo round(($rg_h*100)/($rg_s),2); ?> %</td>
				                <td><? echo round(($pg_h*100)/($pg_s),2); ?> %</td>
				            </tr>
							<tr class="use">
								<td class="title">Use</td>
								<td><? echo round(($gaunt_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($mg_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($sg_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($gl_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($rl_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($lg_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($rg_k*100)/$all_k,2); ?> %</td>
								<td><? echo round(($pg_k*100)/$all_k,2); ?> %</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="box center" id="recent_games">
					<h2>Last 10 Games</h2>

						<table cellspacing="0" border="0" id="ctl00_ContentPlaceHolder1_gridPlayerGames" style="border-collapse:collapse;">
							<tbody>
								<tr>
									<th scope="col">Date</th><th scope="col">Winner</th><th scope="col">P1 Score</th><th scope="col">P2 Score</th><th scope="col">Loser</th><th scope="col">Map</th><th scope="col">Elo Change</th>
								</tr>
								<?
									$duels = mysqli_query($link, "SELECT * FROM stats WHERE WARMUP!='1' AND STEAM_ID='".$_GET[sid]."' GROUP BY MATCH_GUID ORDER by id DESC limit 10");
									while($row = mysqli_fetch_array($duels)){				
										$win = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$row[MATCH_GUID]."' and WIN = 1");
										$winner = mysqli_fetch_array($win);
										$los = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$row[MATCH_GUID]."' and WIN = 0");
										$loser = mysqli_fetch_array($los);				
										$m=mysqli_query($link, "SELECT * FROM reports WHERE MATCH_GUID = '".$row[MATCH_GUID]."'");
										$map = mysqli_fetch_array($m);
										$elo = $row[new_elo] - $row[old_elo];
										if ($elo < 0){
											$elo = "<span style='color:red'>$elo</span>";
										}
										else{
											$elo = "<span style='color:green'>+$elo</span>";
										}
										?>
								<tr>				
										<td><? echo date("d.m.Y", strtotime($row[date])); ?></td>
										<td class="player"><a href="player.php?sid=<? echo $winner[STEAM_ID]; ?>"><? echo quakename($winner[NAME]); ?></a><br><? echo $winner[old_elo]; ?></td>
										<td><span class="score"><? echo $winner[SCORE]; ?></span></td>
										<td><span class="score"><? echo $loser[SCORE]; ?></span></td>										
										<td class="player"><a href="player.php?sid=<? echo $loser[STEAM_ID]; ?>"><? echo quakename($loser[NAME]); ?></a><br><? echo $loser[old_elo]; ?></td>
										<td><a href="match?guid=<? echo $row[MATCH_GUID]; ?>"><img src="images/levelshots/<? echo $map[MAP]; ?>.jpg" style="height:42px;width:56px;border-width:0px;"></a></td>								
										<td><? echo $elo; ?></td>
								</tr>
										<?
									}
								?>								
							</tbody>
						</table>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function () {var mapData = [
	<?
	$m=mysqli_query($link, "SELECT COUNT(*), MAP FROM matches WHERE MATCH_GUID in ($ids) GROUP BY MAP");
	
	while($map = mysqli_fetch_array($m)){
		echo "{label: \"$map[1]\", data: $map[0]},";
	}
	echo mysqli_error();
	?>
	

	]
$.plot( $('#mapgraph') , mapData, 
{series: {pie: {show: true, width: 1000}}, grid: {hoverable: true,clickable: false}});

var trackedDuelData = [{ label: "Amount", data: [[(new Date("2015-09-29").getTime()), 2420],[(new Date("2015-09-30").getTime()), 2318],[(new Date("2015-10-01").getTime()), 2360],[(new Date("2015-10-02").getTime()), 2472],[(new Date("2015-10-03").getTime()), 2746],[(new Date("2015-10-04").getTime()), 2951],[(new Date("2015-10-05").getTime()), 2499],[(new Date("2015-10-06").getTime()), 2387],[(new Date("2015-10-07").getTime()), 2725],[(new Date("2015-10-08").getTime()), 2555],[(new Date("2015-10-09").getTime()), 2649],[(new Date("2015-10-10").getTime()), 2975],[(new Date("2015-10-11").getTime()), 3085],[(new Date("2015-10-12").getTime()), 2711],[(new Date("2015-10-13").getTime()), 2531],[(new Date("2015-10-14").getTime()), 2457],[(new Date("2015-10-15").getTime()), 2369],[(new Date("2015-10-16").getTime()), 2684],[(new Date("2015-10-17").getTime()), 2911],[(new Date("2015-10-18").getTime()), 3115],[(new Date("2015-10-19").getTime()), 2585],[(new Date("2015-10-20").getTime()), 2349],[(new Date("2015-10-21").getTime()), 2439],[(new Date("2015-10-22").getTime()), 2543],[(new Date("2015-10-23").getTime()), 2663],[(new Date("2015-10-24").getTime()), 3178],[(new Date("2015-10-25").getTime()), 2906],[(new Date("2015-10-26").getTime()), 2758],[(new Date("2015-10-27").getTime()), 686]]}];
makeGraph(trackedDuelData, "#trackedgraph", "#CB4B4B", -314, 4178);
$("#mapgraph").mouseout($.hideCursorMessage);
$("#mapgraph").bind("plothover", showCursorMessage);
});
</script>
      <div class="clear"></div>
      <div id="footer">
        <p><a href="https://github.com/sesencheg/ql-stats-php" title="Go to the project page" target="_blank">QL Stats PHP sources</a> are licensed under GPLv2.</p>
      </div>
  </form>
    <script language="javascript" type="text/javascript">
          jQuery.fn.placeholder = function() {
          var value = this.val();

          $(this).focus(function() {
            if (this.value == value)
              this.value = "";
          });

          $(this).blur(function() {
            if (this.value == "")
              this.value = value;
          });
        };

        $('#ctl00_txtPlayerSearch').placeholder();
    </script>
    
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter9954517 = new Ya.Metrika({id:9954517,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/9954517" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
<?
}
?>

