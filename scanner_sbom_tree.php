<?php
  $nav_selected = "SCANNER";
  $left_buttons = "YES";
  $left_selected = "SBOMTREE";
  $blank = "<td> </span> </td>";
  global $db;
  global $pid;
  
  include("./nav.php");
  
 ?>

 <div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Scanner --> BOM Tree</h3>
      <h3><img src="images/sbom_tree.png" style="max-height: 35px;" />System Software BOM</h3>

      <button id="expandAll">Expand All</button>
      <button id="collapseAll">Collapse All</button>
      <button id="noColor">Color / No Color</button>
      <button id="showRed">Show Red</button>
      <button id="showRedYellow">Show Red & Yellow</button>
      <input type="text" id="whereUsedTextInput" placeholder="e.g. Bingo;2.4" />
      <button id="whereUsedSubmit">Where Used</button>

      <table id="sbomTable" cellpadding="0" cellspacing="0" border="0"
            class="datatable table table-striped table-bordered datatable-style table-hover"
            width="100%" style="width: 100px;">
              <thead>
                <div id="table-first-row"> 
                        <th><strong>Sbom Tree</strong></th>
                        <th><strong>App ID</strong></th>
                        <th><strong>App Name</strong></th>
                        <th><strong>App Version</strong></th>
                        <th><strong>CMP ID</strong></th>
                        <th><strong>CMP Name</strong></th>
                        <th><strong>CMP Version</strong></th>
                        <th><strong>CMP Type</strong></th>
                        <th><strong>App Status</strong></th>
                        <th><strong>CMP Status</strong></th>
                        <th><strong>Request ID</strong></th>
                        <th><strong>Request Date</strong></th>
                        <th><strong>Request Status</strong></th>
                        <th><strong>Request Step</strong></th>
                        <th><strong>Notes</th>
                </div>
              </thead>
      <?php
      $count = 0;
      $cmpArray = array();
      $appArray = array();
      $nodeArray = array();
      $nodeIDArray = array();
      $rootYellow = array();

      $appQuery = "SELECT * from sbom ORDER BY request_id ASC;";
        $appRes = $db->query($appQuery);
        $color = "#ecebf0";
        if ($appRes->num_rows > 0) {
          while($row = $appRes->fetch_assoc()) {
            if($pid != $row["app_id"]){
              $count = 0;
              $pid = $row["app_id"];
            }
            $nodeArray[$row["app_id"].$count] = 
            '<tr data-tt-id="'.$row["cmp_id"].'" data-tt-parent-id="'.$row["app_id"].'">
            <td class="green" bgcolor = "#57c95c">'.$row["cmp_name"].' '.$row["cmp_version"].'</td>
            <td>'.$row["app_id"].' </span> </td>
            <td>'.$row["app_name"].' </span> </td>
            <td>'.$row["app_version"].' </span> </td>
            <td>'.$row["cmp_id"].' </span> </td>
            <td>'.$row["cmp_name"].' </span> </td>
            <td>'.$row["cmp_version"].' </span> </td>
            <td>'.$row["cmp_type"].' </span> </td>
            <td>'.$row["app_status"].' </span> </td>
            <td>'.$row["cmp_status"].' </span> </td>
            <td>'.$row["request_id"].' </span> </td>
            <td>'.$row["request_date"].' </span> </td>
            <td>'.$row["request_status"].' </span> </td>
            <td>'.$row["request_step"].' </span> </td>
            <td>'.$row["notes"].' </span> </td>
            </tr>';
            array_push($appArray,$row["app_id"]);
            array_push($cmpArray,$row["cmp_id"]);
            $count++;
          }
        }
        else {
          echo "0 results";
        }//end else

      $appRes->close();
      
      $sql =  "SELECT * FROM sbom ORDER BY app_id,app_name,app_version,cmp_id,cmp_name,cmp_version ASC;";
      //$sql =  "SELECT * FROM sbom ORDER BY request_id ASC;";
          $result = $db->query($sql);
	  $color = "#ecebf0";
          if ($result->num_rows > 0) {
          // output data of each row
              while($row = $result->fetch_assoc()) {
                if($pid != $row["app_id"] && !in_array($row["app_id"],$cmpArray)){ //creates a new app node (root) if the app_id is not a component
                  echo '<tr data-tt-id="'.$row["app_id"].'">
                          <td class="red" bgcolor = "#ff6666">'.$row["app_name"].' '.$row["app_version"].'</td>
                          <td>'.$row["app_id"].' </span> </td>'.
                          $blank.
                          $blank.
                          $blank.
                          $blank.
                          $blank.
                          $blank.
                          '<td>'.$row["app_status"].' </span> </td>'.
                          $blank.
                          $blank.
                          $blank.
                          $blank.
                          $blank.
                          '<td>'.$row["notes"].' </span> </td></tr>';       
                  $pid = $row["app_id"];
                }
                if(in_array($row["cmp_id"],$appArray)){ //if the component is a child application,
                                                        // it pulls the child components of that application
                  echo'<tr data-tt-id="'.$row["cmp_id"].'" data-tt-parent-id="'.$row["app_id"].'">
                      <td class="yellow" bgcolor = "#f5fa69">'.$row["cmp_name"].' '.$row["cmp_version"].'</td>
                      <td>'.$row["app_id"].' </span> </td>
                      <td>'.$row["app_name"].' </span> </td>
                      <td>'.$row["app_version"].' </span> </td>
                      <td>'.$row["cmp_id"].' </span> </td>
                      <td>'.$blank.'</span> </td>
                      <td>'.$blank.'</span> </td>
                      <td>'.$blank.'</span> </td>
                      <td>'.$row["app_status"].' </span> </td>
                      <td>'.$blank.'</span> </td>
                      <td>'.$row["request_id"].' </span> </td>
                      <td>'.$row["request_date"].' </span> </td>
                      <td>'.$row["request_status"].' </span> </td>
                      <td>'.$row["request_step"].' </span> </td>
                      <td>'.$row["notes"].' </span> </td></tr>';
                  $count = 0;
                  while(array_key_exists($row["cmp_id"].$count,$nodeArray)){
                    echo $nodeArray[$row["cmp_id"].$count];
                    $count++;
                  }

                }elseif(!in_array($row["app_id"],$cmpArray)){ //if the component is not also an application and it's also not a 
                                                              //component of a child application, it's set as a child of it's application
                  echo'<tr data-tt-id="'.$row["cmp_id"].'" data-tt-parent-id="'.$row["app_id"].'">
                      <td class="green" bgcolor = "#57c95c">'.$row["cmp_name"].' '.$row["cmp_version"].'</td>
                      <td>'.$row["app_id"].' </span> </td>
                      <td>'.$row["app_name"].' </span> </td>
                      <td>'.$row["app_version"].' </span> </td>
                      <td>'.$row["cmp_id"].' </span> </td>
                      <td>'.$row["cmp_name"].' </span> </td>
                      <td>'.$row["cmp_version"].' </span> </td>
                      <td>'.$row["cmp_type"].' </span> </td>
                      <td>'.$row["app_status"].' </span> </td>
                      <td>'.$row["cmp_status"].' </span> </td>
                      <td>'.$row["request_id"].' </span> </td>
                      <td>'.$row["request_date"].' </span> </td>
                      <td>'.$row["request_status"].' </span> </td>
                      <td>'.$row["request_step"].' </span> </td>
                      <td>'.$row["notes"].' </span> </td></tr>';
                }

                  
              }//end while
          }//end if
          else {
              echo "0 results";
          }//end else

       $result->close();

      ?>

      </table>

    </div>
</div>

<link href="jquery.treetable.css" rel="stylesheet" type="text/css" />
<link href="jquery.treetable.theme.default.css" rel="stylesheet" type="text/css" />
<script src="jquery.treetable.js"></script>

<script>

$(document).ready(function(){
  $('#sbomTable').DataTable( {
            dom: 'lfrtBip'}
        );
  $("#whereUsedTextInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#sbomTable td").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });


var color = 0;
var tree = $("#sbomTable").treetable({expandable: true, initialState: "collapsed"});

$("#expandAll").click(function(expand) {
   tree.treetable('destroy');
   tree.find(".indenter").remove();
   tree.treetable({expandable: true, initialState: "expanded"});
});

$("#collapseAll").click(function(collapse) {
   tree.treetable('destroy');
   tree.find(".indenter").remove();
   tree.treetable({expandable: true, initialState: "collapsed"});
});

//testing move function
$("#showRed").click(function(showR){
  $("#sbomTable").treetable('move','101.1','');
});
//testing move function
$("#showRedYellow").click(function(showR){
  $("#sbomTable").treetable('move','101.1','');
});
$("#noColor").click(function(showR){
  if (color == 0){
   $('.red').css('background-color', '#f8f7fa');
   $('.yellow').css('background-color', '#f8f7fa');
   $('.green').css('background-color', '#f8f7fa');
   color = 1 ; 
  }
  else {
   $('.red').css('background-color', '#ff6666');
   $('.yellow').css('background-color', '#f5fa69');
   $('.green').css('background-color', '#57c95c');
   color = 0;
  }
});


</script>

<script>



<?php include("./footer.php"); ?>


