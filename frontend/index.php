<?php
	require_once('config.php');
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

          
          
    <div id="top">
    <div class="box" id="last_scanned">
      <h2>Last Scanned Matches</h2>
      <div>
	<table cellspacing="0" border="0" id="ctl00_ContentPlaceHolder1_gridLast5" style="border-collapse:collapse;">
		<tr>
			<th scope="col">Winner</th><th scope="col">Score</th><th scope="col">Loser</th><th scope="col">Map</th>
		</tr>
		<?
			$duels = mysqli_query($link, "SELECT * FROM stats, matches WHERE stats.WARMUP!='1' AND stats.MATCH_GUID = matches.MATCH_GUID AND matches.GAME_TYPE='DUEL' GROUP BY stats.MATCH_GUID ORDER by matches.id DESC limit 10");
			while($row = mysqli_fetch_array($duels)){				
				$win = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$row[MATCH_GUID]."' AND WIN = 1");
				$winner = mysqli_fetch_array($win);
				$los = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$row[MATCH_GUID]."' AND WIN = 0");
				$loser = mysqli_fetch_array($los);				
				$m=mysqli_query($link, "SELECT * FROM matches WHERE MATCH_GUID = '".$row[MATCH_GUID]."'");
				$map = mysqli_fetch_array($m);
				?>
		<tr>				
				
				<td class="player"><a href="player.php?sid=<? echo $winner[STEAM_ID]; ?>"><? echo quakename($winner[NAME]); ?></a></td>
				<td><b><? echo $winner[SCORE]; ?> </b>:<b> <? echo $loser[SCORE]; ?></b></td>
				<td class="player"><a href="player.php?sid=<? echo $loser[STEAM_ID]; ?>"><? echo quakename($loser[NAME]); ?></a></td>
				<td><? echo $map[MAP]; ?></td>								
		</tr>
				<?
			}
		?>		
	</table>
</div>
    </div>
  </div>
  <div class="left">
      <div class="box">
        <h2>
          Top 100 Duel Players
        </h2>
        <div id="ctl00_ContentPlaceHolder1_UpdatePanel2">
	
              <div>
		<table cellspacing="0" border="0" id="ctl00_ContentPlaceHolder1_gridTop100" style="border-collapse:collapse;">
			<tbody>
			<tr>
				<th scope="col">Rank</th><th scope="col">Nickname</th><th scope="col">ELO</th>
			</tr>
			<?			
				$t = mysqli_query($link, "SELECT * FROM stats, players WHERE stats.STEAM_ID = players.STEAM_ID GROUP BY stats.STEAM_ID ORDER by players.DUEL_ELO DESC");
				$n=1;
				while($top = mysqli_fetch_array($t)){
					?>
					<tr>
						<td><? echo $n; ?></td>
						<td class="player"><a href="player.php?sid=<? echo $top[STEAM_ID]; ?>"><? echo quakename($top[NAME]); ?></a></td>
						<td><? echo $top[DUEL_ELO]; ?></td>						
					</tr>
					<?
					$n++;
				}
			?>			
			</tbody></table>
	</div>
            
</div>
	</div>
            
</div>

  <div class="right">
    <div class="box" id="most-played">
      <h2>Most Popular Duel Maps</h2>
      <div id="mapgraph" class="graph"></div>
    </div>
    <div class="box">
    	<h2>
        	Top Medals
        </h2>    
		<table cellspacing="0" border="0" id="ctl00_ContentPlaceHolder1_gridTop100" style="border-collapse:collapse;">
			<tbody>
				<tr>
					<td><img src="images/medals/medal_impressive.png"></td>
					<?
						$m1=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'IMPRESSIVE' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med1 = mysqli_fetch_array($m1);
					?>	
					<td><? echo $med1[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med1[2]; ?>"><? echo quakename($med1[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_midair.png"></td>
					<?
						$m2=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'MIDAIR' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med2 = mysqli_fetch_array($m2);
					?>	
					<td><? echo $med2[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med2[2]; ?>"><? echo quakename($med2[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_combokill.png"></td>
					<?
						$m3=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'COMBOKILL' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med3 = mysqli_fetch_array($m3);
					?>	
					<td><? echo $med3[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med3[2]; ?>"><? echo quakename($med3[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_headshot.png"></td>
					<?
						$m3=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'HEADSHOT' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med3 = mysqli_fetch_array($m3);
					?>	
					<td><? echo $med3[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med3[2]; ?>"><? echo quakename($med3[1]); ?></a></td>
				</tr>														
				<tr>
					<td><img src="images/medals/medal_gauntlet.png"></td>
					<?
						$m4=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'GAUNTLET' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med4 = mysqli_fetch_array($m4);
					?>	
					<td><? echo $med4[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med4[2]; ?>"><? echo quakename($med4[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_revenge.png"></td>
					<?
						$m5=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'REVENGE' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med5 = mysqli_fetch_array($m5);
					?>	
					<td><? echo $med5[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med5[2]; ?>"><? echo quakename($med5[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_excellent.png"></td>
					<?
						$m6=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'EXCELLENT' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med6 = mysqli_fetch_array($m6);
					?>	
					<td><? echo $med6[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med6[2]; ?>"><? echo quakename($med6[1]); ?></a></td>
				</tr>
				<tr>
					<td><img src="images/medals/medal_firstfrag.png"></td>
					<?
						$m7=mysqli_query($link, "SELECT COUNT(*), NAME, STEAM_ID FROM medals WHERE MEDAL = 'FIRSTFRAG' GROUP BY STEAM_ID ORDER BY COUNT(*) DESC LIMIT 1");	
						$med7 = mysqli_fetch_array($m7);
					?>	
					<td><? echo $med7[0]; ?></td>
					<td class="player"><a href="player.php?sid=<? echo $med7[2]; ?>"><? echo quakename($med7[1]); ?></a></td>
				</tr>																			
			</tbody>
		</table>
	</div>       
  </div>
<script type="text/javascript">
$(document).ready(function () {var mapData = [
	<?
	$m=mysqli_query($link, "SELECT COUNT(*), MAP FROM matches GROUP BY MAP");
	while($map = mysqli_fetch_array($m)){
		if ($map[0] > 5) {
			echo "{label: \"$map[1]\", data: $map[0]},";
		}
	}

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
        </div>
      </div>
      <div class="clear"></div>
      <div id="footer">

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
</body>
</html>

