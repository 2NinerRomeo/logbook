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

   //Total hours (no specific aircraft/flight requirements)
   $pageRes = mysql_query("SELECT COUNT(id) AS flight_count,
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
                         FROM flights
                         WHERE 
                          flights.pageId = ".$pageRecord->val('id'),$app->db());
                         //FROM flights JOIN airplanes ON flights.aircraftId = airplanes.id
                         //airplanes.landinggear = TAILDRAGGER
                         //AND

   if(!$pageRes)
   {
      echo "Errormessage: ";
      echo mysql_error();
      //echo "Nothing found in first Taildragger SQL query...";
   }

//   $row = mysql_fetch_assoc($pageRes);
//   mysql_free_result($pageRes);

//   echo "Flights on page: ";
//   echo $row['numflights']."<br>";
//   var_dump($row);
   echo "<br>";

   $row = array("pages"=>"This page") + $row;
   $data[] = $row;


   //////////////////////////////////////
   // Queries for data UP TO this page //
   //////////////////////////////////////

   //Total hours (no specific aircraft/flight requirements)
   $pageRes = mysql_query("SELECT COUNT(id) AS flight_count,
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
   $row = array("pages"=>"To date") + $row;

//   echo "Flights through this page: ";
//   echo $row['numflights']."<br>";
//   var_dump($row);
   echo "<br>";

   mysql_free_result($pageRes);

   $data[] = $row;

//   echo "<br>";
//   var_dump($data);

   $grid = new Dataface_RecordGrid($data);  
   echo $grid->toHTML();

   echo "Todo:<br>";
   echo "Taildragger Landings   <br>";

   echo "Single Engine<br>";
   echo "Multi Engine<br>";
   echo "High Performance<br>";
   echo "Complex<br>";
   echo "Taildragger<br>";

   echo "CC<br>";
   echo "PIC CC<br>";
   echo "Solo CC<br>";

   echo "Ground Trainer<br>";
   echo "Dual<br>";
   echo "Solo<br>";
}

}
?>