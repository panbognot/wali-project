<div id="float">
  <ul class="list-group list-unstyled">
    <li id="maps" class="dropdown">
    <?php
      $sql="SELECT clusterid, name FROM cluster";
      $result=mysql_query($sql);
      $clustersArray = array();
      $countClusters = 0;
      while($row=mysql_fetch_array($result))
      {
        $clustersArray[$countClusters]['clusterid'] = $row['clusterid'];
        $clustersArray[$countClusters]['name'] = $row['name'];
        $countClusters++;
      }
      mysql_free_result($result);
    ?>
      <a href="#" class="list-group-item <?php echo $groupWarningMaps; ?> dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-map-marker"></span>
        Maps
        <span class="badge pull-right <?php echo $badgeWarningMaps; ?>"><?php echo $countClusters; ?></span>
      </a>
      <ul class="dropdown-menu">
        <li role="presentation" class="dropdown-header">Map Clusters</li>
        <?php
          for($i = 0; $i < $countClusters; $i++) {
            echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./clusterview.php?clusterid=".$clustersArray[$i]['clusterid']."\">".$clustersArray[$i]['name']."</a></li>";
          }
        ?>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="./addcluster.php">Add a Cluster</a></li>
          </ul>
    </li>
    <li id="lights" class="dropdown">
    <?php
      $sql="SELECT bulbid, name FROM bulb";
      $result=mysql_query($sql);
      $bulbsArray = array();
      $countBulbs = 0;
      while($row=mysql_fetch_array($result))
      {
        $bulbsArray[$countBulbs]['bulbid'] = $row['bulbid'];
        $bulbsArray[$countBulbs]['name'] = $row['name'];
        $countBulbs++;
      }
      mysql_free_result($result);
    ?>
      <a href="#" class="list-group-item <?php echo $groupWarningLights; ?> dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-adjust"></span>
        Lights
        <span class="badge pull-right <?php echo $badgeWarningLights; ?>"><?php echo $countBulbs; ?></span>
      </a>
      <ul class="dropdown-menu">
        <li role="presentation" class="dropdown-header">Light Bulbs</li>
        <?php
          for($i = 0; $i < $countBulbs; $i++) {
            echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./view.php?bulbid=".$bulbsArray[$i]['bulbid']."\">".$bulbsArray[$i]['name']."</a></li>";
          }
        ?>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="./addlight.php">Add a Light</a></li>
            </ul>
    </li>
    <li id="readings" class="dropdown">
    <?php
      $sql="SELECT bulbid, name FROM bulb WHERE bulbid IN (SELECT DISTINCT bulbid FROM poweranalyzer ORDER BY bulbid)";
      $result=mysql_query($sql);
      $readingsArray = array();
      $countReadings = 0;
      while($row=mysql_fetch_array($result))
      {
        $readingsArray[$countReadings]['bulbid'] = $row['bulbid'];
        $readingsArray[$countReadings]['name'] = $row['name'];
        $countReadings++;
      }
      mysql_free_result($result);
    ?>
      <a href="#" class="list-group-item <?php echo $groupWarningReportsIndividual; ?> dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-signal"></span>
        Reports
        <span class="badge pull-right <?php echo $badgeWarningReportsIndividual; ?>"><?php echo $countReadings; ?></span>
      </a>
      <ul class="dropdown-menu">
        <li role="presentation" class="dropdown-header">Consumption Reports</li>
        <?php
          for($i = 0; $i < $countReadings; $i++) {
            echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./readings.php?bulbid=".$readingsArray[$i]['bulbid']."\">".$readingsArray[$i]['name']."</a></li>";
          }
        ?>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Customize a Report</a></li>
          </ul>
    </li>
    <li id="schedules" class="dropdown">
    <?php
      $sql="SELECT scheduleid, start_date, end_date, start_time, end_time FROM schedule";
      $result=mysql_query($sql);
      $schedulesArray = array();
      $countSchedules = 0;
      while($row=mysql_fetch_array($result))
      {
        $schedulesArray[$countSchedules]['scheduleid'] = $row['scheduleid'];
        $schedulesArray[$countSchedules]['start_date'] = $row['start_date'];
        $schedulesArray[$countSchedules]['start_time'] = $row['start_time'];
        $schedulesArray[$countSchedules]['end_date'] = $row['end_date'];
        $schedulesArray[$countSchedules]['end_time'] = $row['end_time'];
        $countSchedules++;
      }
      mysql_free_result($result);
    ?>
      <a href="#" class="list-group-item <?php echo $groupWarningSchedules; ?> dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-calendar"></span>
        Schedules
        <span class="badge pull-right <?php echo $badgeWarningSchedules; ?>"><?php echo $countSchedules; ?></span>
      </a>
      <ul class="dropdown-menu">
        <li role="presentation" class="dropdown-header">Events</li>
        <?php
          $dateNow = date("Y-m-d");
          for($i = 0; $i < $countSchedules; $i++) {
            if ($schedulesArray[$i]['end_date'] > $dateNow)
              echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./viewschedule.php?scheduleid=".$schedulesArray[$i]['scheduleid']."\">On ".$schedulesArray[$i]['start_date']." to ".$schedulesArray[$i]['end_date']." from ".$schedulesArray[$i]['start_time']." to ".$schedulesArray[$i]['end_time']."</a></li>";
          }
        ?>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="./addschedule.php">Schedule an Event</a></li>
          </ul>
    </li>   
  </ul>
</div>