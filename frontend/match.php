<?php
require_once('config.php');
if (isset($_GET["guid"])){
	$match = mysqli_query($link, "SELECT * FROM reports WHERE MATCH_GUID='".$_GET[guid]."'");
 	$mm = mysqli_fetch_array($match);
	$win = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$mm[MATCH_GUID]."' and WIN = 1");
	$winner = mysqli_fetch_array($win);
	$los = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$mm[MATCH_GUID]."' and WIN = 0");
	$loser = mysqli_fetch_array($los);
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
        <div class="inner" style="min-height: 680px; padding-top: 20px; background: url('images/maps/<?php echo $mm[MAP]; ?>.jpg'); background-size: cover;">
        	<div id="player">
				<div class="left-sixth" style="text-align:center;">
					<h2><? echo quakename($winner[NAME]);?></h2>
            		<div id="avatar">
                		<img id="ctl00_ContentPlaceHolder1_imgModel" src="images/models/<? echo $winner[MODEL];?>.png" style="border-width:0px;">
            		</div>
            		
            		<div id="medals">
            			<?php
            			 $medexc=0;
            			 $medimp=0;
            			 $medhum=0;
						 $m = mysqli_query($link, "SELECT *,COUNT(*) as summed FROM medals WHERE WARMUP!='1' AND STEAM_ID='".$winner[STEAM_ID]."' AND MATCH_GUID = '".$mm[MATCH_GUID]."' GROUP BY MEDAL");
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
				<div class="left-twothird" style="text-align: center; width: 67%; background: #333; opacity: 0.7; filter: alpha(Opacity=70);">
					<?
						$dtg_w = json_decode(str_replace("'", '"', $winner[WEAPONS]));					                        		
						$dtg_l = json_decode(str_replace("'", '"', $loser[WEAPONS]));
						$damage = json_decode(str_replace("'", '"', $winner[DAMAGE]));	

					?>					
				<table class="statik">
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right; font-size: 42px; font-weight: bold;"><?php echo $winner[SCORE];?></td>						
						<td></td>
						<td colspan="2" style="text-align: left; font-size: 42px; font-weight: bold;"><?php echo $loser[SCORE];?></td>						
						<td></td>
					</tr>
					<tr>
						<td style="text-align: center; color: #22d6d9">shots/hits</td>
						<td style="text-align: center;">accuracy</td>						
						<td style="text-align: right;">frags</td>
						<td style="text-align: center;"></span></td>
						<td style="text-align: left;">frags</td>
						<td style="text-align: center;">accuracy</td>	
						<td style="text-align: center; color: #22d6d9">shots/hits</td>
					</tr>						
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->GAUNTLET->H;?>/<?php echo $dtg_w->GAUNTLET->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->GAUNTLET->H*100)/($dtg_w->GAUNTLET->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->GAUNTLET->K;?></td>
						<td style="text-align: center;"><span class="icon gauntlet"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->GAUNTLET->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->GAUNTLET->H*100)/($dtg_l->GAUNTLET->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->GAUNTLET->H;?>/<?php echo $dtg_l->GAUNTLET->S;?></td>
					</tr>
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->MACHINEGUN->H;?>/<?php echo $dtg_w->MACHINEGUN->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->MACHINEGUN->H*100)/($dtg_w->MACHINEGUN->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->MACHINEGUN->K;?></td>
						<td style="text-align: center;"><span class="icon mg"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->MACHINEGUN->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->MACHINEGUN->H*100)/($dtg_l->MACHINEGUN->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->MACHINEGUN->H;?>/<?php echo $dtg_l->MACHINEGUN->S;?></td>
					</tr>
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->SHOTGUN->H;?>/<?php echo $dtg_w->SHOTGUN->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->SHOTGUN->H*100)/($dtg_w->SHOTGUN->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->SHOTGUN->K;?></td>
						<td style="text-align: center;"><span class="icon sg"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->SHOTGUN->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->SHOTGUN->H*100)/($dtg_l->SHOTGUN->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->SHOTGUN->H;?>/<?php echo $dtg_l->SHOTGUN->S;?></td>
					</tr>	
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->GRENADE->H;?>/<?php echo $dtg_w->GRENADE->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->GRENADE->H*100)/($dtg_w->GRENADE->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->GRENADE->K;?></td>
						<td style="text-align: center;"><span class="icon gl"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->GRENADE->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->GRENADE->H*100)/($dtg_l->GRENADE->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->GRENADE->H;?>/<?php echo $dtg_l->GRENADE->S;?></td>
					</tr>	
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->ROCKET->H;?>/<?php echo $dtg_w->ROCKET->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->ROCKET->H*100)/($dtg_w->ROCKET->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->ROCKET->K;?></td>
						<td style="text-align: center;"><span class="icon rl"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->ROCKET->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->ROCKET->H*100)/($dtg_l->ROCKET->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->ROCKET->H;?>/<?php echo $dtg_l->ROCKET->S;?></td>
					</tr>																											
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->LIGHTNING->H;?>/<?php echo $dtg_w->LIGHTNING->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->LIGHTNING->H*100)/($dtg_w->LIGHTNING->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->LIGHTNING->K;?></td>
						<td style="text-align: center;"><span class="icon lg"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->LIGHTNING->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->LIGHTNING->H*100)/($dtg_l->LIGHTNING->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->LIGHTNING->H;?>/<?php echo $dtg_l->LIGHTNING->S;?></td>
					</tr>	
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->RAILGUN->H;?>/<?php echo $dtg_w->RAILGUN->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->RAILGUN->H*100)/($dtg_w->RAILGUN->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->RAILGUN->K;?></td>
						<td style="text-align: center;"><span class="icon rg"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->RAILGUN->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->RAILGUN->H*100)/($dtg_l->RAILGUN->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->RAILGUN->H;?>/<?php echo $dtg_l->RAILGUN->S;?></td>
					</tr>		
					<tr>
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_w->PLASMA->H;?>/<?php echo $dtg_w->PLASMA->S;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_w->PLASMA->H*100)/($dtg_w->PLASMA->S),2); ?> %</td>						
						<td style="text-align: right;"><?php echo $dtg_w->PLASMA->K;?></td>
						<td style="text-align: center;"><span class="icon pg"></span></td>
						<td style="text-align: left;"><?php echo $dtg_l->PLASMA->K;?></td>
						<td style="text-align: center;"><?php echo round(($dtg_l->PLASMA->H*100)/($dtg_l->PLASMA->S),2); ?> %</td>	
						<td style="text-align: center; color: #22d6d9"><?php echo $dtg_l->PLASMA->H;?>/<?php echo $dtg_l->PLASMA->S;?></td>
					</tr>										
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right;"><?php echo $damage->DEALT;?></td>						
						<td style="text-align: center;"><img src="images/medals/medal_excellent.png" width="28px"></td>
						<td colspan="2" style="text-align: left;"><?php echo $damage->TAKEN;?></td>						
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right;"><? echo $winner[old_elo];?></td>						
						<td style="text-align: center;">ELO</td>
						<td colspan="2" style="text-align: left;"><? echo $loser[old_elo];?></td>						
						<td></td>
					</tr>											
				</table>					

        		</div>     
				<div class="right-sixth" style="text-align:center;">
					<h2><? echo quakename($loser[NAME]);?></h2>
            		<div id="avatar">
                		<img id="ctl00_ContentPlaceHolder1_imgModel" src="images/models/<? echo $loser[MODEL];?>.png" style="border-width:0px; transform: scale(-1, 1)">
            		</div>
            		
            		<div id="medals">
            			<?php
            			 $medexc=0;
            			 $medimp=0;
            			 $medhum=0;
						 $m = mysqli_query($link, "SELECT *,COUNT(*) as summed FROM medals WHERE WARMUP!='1' AND STEAM_ID='".$loser[STEAM_ID]."' AND MATCH_GUID = '".$mm[MATCH_GUID]."' GROUP BY MEDAL");
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
			</div>
		</div>
	</div>
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

