<?php
$dataset1 = array(array("c150", "100"),array( "c172", "world");
class actions_time_in_type
{
  function handle(&$params)
  {
    $this->app =& Dataface_Application::getInstance();
    $body = "<br /><br />";
    import( 'Dataface/RecordGrid.php');

    //The Query
    $result = mysql_query("SELECT airplanes.typeDes, COUNT(*) as flights, SUM(flights.day + flights.night) AS total FROM airplanes INNER JOIN flights ON airplanes.id = flights.aircraftid GROUP BY typeDes", $this->app->db());

    global $dataset1;

    //for some reason, this syntax does not seem to touch $dataset1
    array_push($dataset1, "Andrew", "and Natalie");

    //This syntax is working, but it is not the same as the global dataset1
    $dataset1[] = "were Here"; 
    $dataset1[] = "and There"; 
    if(!$result)
    {
       // Error handling
       $body .= "MySQL Error...";
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          // Maybe do something with the single rows
          $data[] = $row;   // Add single gow to the data
//          $dataset1[] = array( $row['typedes'], $row['total'] );
//          $dataset1[] = array( $row['typedes']] );
       }
       mysql_free_result($result); //Frees the result after finished using it
       
       $grid = new Dataface_RecordGrid($data);  // Create new RecordGrid with the data
       $body .= $grid->toHTML();   // Get the HTML of the RecordGrid

       var_dump($dataset1); //dumps into html, doesn't get into $body
       $body .= "<div id=\"typePieChart\" style=\"width:600px;height:300px;\"><div>";

    }
    // Shows the content (RecordGrid or error message) in the main Template
    df_display(array('body' => $body), 'Dataface_Main_Template.html');
  }
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.pie.min.js"></script>
<script type="text/javascript">

//don't wait for page completion for this to run
var dataset2 = <?php echo json_encode(utf8_encode($dataset2)); ?>;
var dataset1 = <?php echo json_encode($dataset1); ?>;

$(function () {
	var d4 = [],
      series = Math.floor(Math.random() * 6) + 3;

	for (var i = 0; i < series; i++) 
   {
		d4[i] = 
      {
			label: "Series" + (i + 1),
			data: Math.floor(Math.random() * 100) + 1
      }
   }    
   alert(d4.join('\n'));

   //alert(dataset1.join('\n'));
   alert(dataset1);  //coming up null...still

//   $.plot($("#typePieChart"), [d1, d2, d3]);
//   $.plot($("#typePieChart"), dataset1);
   $.plot($("#typePieChart"), d4, {series: { pie: {show:true}}});
});
</script>





