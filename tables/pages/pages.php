<?php
class tables_pages
{

function getTitle(&$record)
{
   return "Book ".$record->val(logbook). " Page ".$record->val(pageNumber);
}

function titleColumn()
{
   return "CONCAT('Book ',logbook,' Page ',pageNumber)";
}

//function block__after_record_content()
//{
//   echo "After Record Content";   
//}

function block__before_record_heading()
{
   echo "Before Record Heading";   
}

function block__before_details_controller()
{
   echo "Before Details Controller";   
}

function block__after_record_actions()
{
   echo "After Record Actions";
}

function block__before_view_tab_content()
{
   echo "Before view tab content";
}

function block__after_view_tab_content()
{
   import( 'Dataface/RecordGrid.php');

   // Obtain reference to current page record
   $app =& Dataface_Application::getInstance();
   $pageRecord =& $app->getRecord();

   ///////////////////////////////////
   // Queries for data on THIS PAGE //
   ///////////////////////////////////

   $target_row = array(
     "pages" => "error",
     "flight_count" => "error",
     "inst_apch" => "error",
     "landings" => "error",
     "tw_landings" => "error",
     "tw_hours" => "error",
     "se_hours" => "error",
     "me_hours" => "error",
     "cross_country" => "error",
     "day" => "error",
     "night" => "error",
     "sim_imc" => "error",
     "act_imc" => "error",
     "dual_hours" => "error",
     "pic" => "error",
     "total_hours" => "error"
   );

//   echo "Target row:<br/>\n";
//   var_dump($target_row);
//   echo "<br>\n";

   ///////////////////////////////////
   // Queries for data on THIS PAGE //
   ///////////////////////////////////

   //Total hours (no specific aircraft/flight requirements)
   $pageRes = mysql_query("SELECT COUNT(id) AS flight_count,
                         SUM(flights.instapch) AS inst_apch,
                         SUM(flights.landings) AS landings,
                         SUM(flights.day) as day,
                         SUM(flights.night) as night,
                         SUM(flights.simulated) as sim_imc,
                         SUM(flights.actual) as act_imc,
                         SUM(flights.pic) AS pic,
                         SUM(flights.day + flights.night) AS total_hours
                         FROM flights 
                         WHERE flights.pageId = ".$pageRecord->val('id'),$app->db());

   if(!$pageRes)
   {
      echo "Nothing found in first SQL query...";
   }

   $row = mysql_fetch_assoc($pageRes);

   mysql_free_result($pageRes);


   //Taildragger landings an hours
   $pageRes = mysql_query("SELECT SUM(flights.landings) AS tw_landings,
                         SUM(flights.day + flights.night) AS tw_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId = " . $pageRecord->val('id') . "
                         AND airplanes.landinggear = 'TAILDRAGGER'",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in first Taildragger SQL query...<br/>\n";
   }

   $tdrow = mysql_fetch_assoc($pageRes);
   //echo "Taildragger Row: <br/>";
   //var_dump($tdrow);
   //echo "<br>";
   mysql_free_result($pageRes);

   //Single Engine hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS se_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId = " . $pageRecord->val('id') . "
                         AND airplanes.class = 8",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in Single Engine Query...<br/>\n";
   }

   $serow = mysql_fetch_assoc($pageRes);
   //echo "Taildragger Row: <br/>";
   //var_dump($tdrow);
   //echo "<br>";
   mysql_free_result($pageRes);

   //Multi Engine hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS me_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId = " . $pageRecord->val('id') . "
                         AND airplanes.class = 9",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in Multi Engine Query...<br/>\n";
   }

   $merow = mysql_fetch_assoc($pageRes);
   //echo "Taildragger Row: <br/>";
   //var_dump($tdrow);
   //echo "<br>";
   mysql_free_result($pageRes);

   //Cross Country hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS cross_country
                         FROM flights 
                         WHERE flights.pageId = ".$pageRecord->val('id').
			 " AND flights.crossCountry" , $app->db());

   $ccrow = mysql_fetch_assoc($pageRes);
   mysql_free_result($pageRes);

   //Dual hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS dual_hours
                           FROM flights
                           WHERE flights.pageId = " . $pageRecord->val('id') .
                           " AND flights.instructorId" , $app->db());

   $dualrow = mysql_fetch_assoc($pageRes);
   mysql_free_result($pageRes);

   if(!$pageRes)
   {
      echo "Errormessage: <br>\n";
      echo mysql_error() ."<br>\n";
      echo "Nothing found in Dual query...<br>\n";
   }
   


   $target_row = array_merge(
     $target_row,
     $row,
     array("pages"=>"This Page"),
     $serow,
     $merow,
     $ccrow,
     $dualrow,
     $tdrow);

   //var_dump($target_row);
   //echo "<br>\n";
   $data[] = $target_row;


   ///////////////////////////////////
   // Clear out Data in $target_row //
   ///////////////////////////////////
   foreach($target_row as $key => $value)
      $target_row[$key] = "error";

//   echo "Empty Target row:<br/>\n";
//   var_dump($target_row);
//   echo "<br/>\n";


   //////////////////////////////////////
   // Queries for data UP TO this page //
   //////////////////////////////////////

   //Total hours (no specific aircraft/flight requirements)
   $pageRes = mysql_query("SELECT COUNT(id) AS flight_count,
                         SUM(flights.instapch) AS inst_apch,
                         SUM(flights.landings) AS landings,
                         SUM(flights.day) as day,
                         SUM(flights.night) as night,
                         SUM(flights.simulated) as sim_imc,
                         SUM(flights.actual) as act_imc,
                         SUM(flights.pic) AS pic,
                         SUM(flights.day + flights.night) AS total_hours
                         FROM flights 
                         WHERE flights.pageId <= ".$pageRecord->val('id'),$app->db());
   if(!$pageRes)
   {
      echo "Nothing found in second SQL query...<br>";
   }

   $row = mysql_fetch_assoc($pageRes);
   //Cross Country hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS cross_country
                         FROM flights 
                         WHERE flights.pageId <= ".$pageRecord->val('id').
			 " AND flights.crossCountry" , $app->db());

   $ccToDateRow = mysql_fetch_assoc($pageRes);
   mysql_free_result($pageRes);

   //Dual Hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS dual_hours
                           FROM flights
                           WHERE flights.pageId <= " . $pageRecord->val('id') .
                           " AND flights.instructorId" , $app->db());

   $dualToDateRow = mysql_fetch_assoc($pageRes);
   mysql_free_result($pageRes);

   //Taildragger landings an hours
   $pageRes = mysql_query("SELECT SUM(flights.landings) AS tw_landings,
                         SUM(flights.day + flights.night) AS tw_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId <= " . $pageRecord->val('id') . "
                         AND airplanes.landinggear = 'TAILDRAGGER'",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in first Taildragger SQL query...<br/>\n";
   }

   $tdrow = mysql_fetch_assoc($pageRes);

   //Single Engine hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS se_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId <= " . $pageRecord->val('id') . "
                         AND airplanes.class = 8",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in Single Engine Query...<br/>\n";
   }

   $serow = mysql_fetch_assoc($pageRes);
   //echo "Single Engine Row: <br/>";
   //var_dump($serow);
   //echo "<br>";
   mysql_free_result($pageRes);

   //Multi Engine hours
   $pageRes = mysql_query("SELECT SUM(flights.day + flights.night) AS me_hours
                         FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         WHERE flights.pageId <= " . $pageRecord->val('id') . "
                         AND airplanes.class = 9",$app->db());

   if(!$pageRes)
   {
      echo "Errormessage: <br/>\n";
      echo mysql_error() . "<br/>\n";
      echo "Nothing found in Multi Engine Query...<br/>\n";
   }

   $merow = mysql_fetch_assoc($pageRes);
   //echo "Taildragger Row: <br/>";
   //var_dump($tdrow);
   //echo "<br>";
   mysql_free_result($pageRes);

   $target_row = array_merge(
     $target_row,
     $row,
     array("pages"=>"To Date"),
     $serow,
     $merow,
     $ccToDateRow,
     $dualToDateRow,
     $tdrow);

   $data[] = $target_row;

   ////////////////////////////////////
   // End Queries, Start outputting  //
   ////////////////////////////////////

//  Dumping row and data to see structural difference
//   var_dump($row);
//   echo "<br>\n";
//   var_dump($data);

   echo "<br>\n";

   $grid = new Dataface_RecordGrid($data);
   echo $grid->toHTML();


//   echo "<br>";
//   var_dump($data);

   echo "Todo:<br>\n";
   //echo "Taildragger Landings: " . $tdrow["tw_landings"] . " <br>\n";

   echo "Single Engine<br>\n";
   echo "Multi Engine<br>\n";
   echo "High Performance<br>\n";
   echo "Complex<br>\n";

   echo "PIC CC<br>\n";
   echo "Solo CC<br>\n";

   echo "Ground Trainer<br>\n";
   echo "Solo<br>\n";
}

}
?>