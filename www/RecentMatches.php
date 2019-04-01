<?php
session_start();
require_once("header.php");
?>
<h3>Showing 100 most recent results.</h3>
<?php
$sql = "SELECT DATE_FORMAT(`Date`, '%Y-%m-%d %H:%i:%s') as Date,
       participant1.Name as Bot1,
	   participant1.ID as Bot1ID,
       participant2.Name as Bot2,
	   participant2.ID as Bot2ID,
       Map,
       Winner,
       Crash,
       Result,
       ReplayFile,
       Bot1Change,
       Bot2Change,
       Bot1AvgFrame,
       Bot2AvgFrame,
       Frames
FROM results
INNER JOIN participants AS participant1 ON participant1.ID = results.Bot1
INNER JOIN participants AS participant2 ON participant2.ID = results.Bot2
ORDER BY `Date` DESC
LIMIT 100";

$result = $link->query($sql);
if ($result->num_rows < 1) {
    echo "<p>No Results available</p>";
    die();
}
?>
<table id="RecentMatchesTable" class="table table-striped" style="width: auto;">
    <thead>
        <tr>
            <?php $column_id = 0; ?>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Date</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot1</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot2</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Map</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Winner</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Result</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">ReplayFile</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot1Change</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot2Change</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot1AvgFrame</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Bot2AvgFrame</th>
            <th class="sortable" onclick="bubbleSortTable('RecentMatchesTable', <?php echo $column_id++ ?>)">Time</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td> <?php echo htmlspecialchars($row['Date']); ?>  </td>
            <td> <?php echo htmlspecialchars($row['Bot1']); ?>  </td>
            <td> <?php echo htmlspecialchars($row['Bot2']); ?>  </td>
            <td> <?php echo htmlspecialchars($row['Map']); ?>  </td>
            <td> 
                <?php 
                if($row['Winner'] == $row['Bot1ID'] )
                {
                    echo htmlspecialchars($row['Bot1']);
                }
                else if ($row['Winner'] == $row['Bot2ID'])
                {
                    echo htmlspecialchars($row['Bot2']);
                }
                ?>  </td>
            <td> <?php echo $row['Result']; ?>  </td>
            <td>
                <a class="btn btn-info navbar-btn download-btn" href="<?php echo $row['ReplayFile'] ?>">
                    <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
                    <span>Replay</span>
                </button>
            </td>
            <td> <?php echo $row['Bot1Change']; ?>  </td>
            <td> <?php echo $row['Bot2Change']; ?>  </td>
            <td> <?php echo $row['Bot1AvgFrame']; ?>  </td>
            <td> <?php echo $row['Bot2AvgFrame']; ?>  </td>
            <td> <?php 
                if($row["Frames"] > 0)
                {
                    $seconds = $row["Frames"] / 22.4;
                    echo gmdate("H:i:s", $seconds);
                } ?> 
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php require_once("footer.php"); ?>
</body>
</html>
