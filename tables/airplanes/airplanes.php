<?php
class tables_airplanes
{
   /**
    * Inserts content at beginning of left column of application.
    * i.e. replaces {block name="before_left_column" tag.
    */
   function block__before_left_column()
   {
      echo "<div id=\"flights-sidebar\">
         <h4>Flights in this Airplane</h4>";

      // Obtain reference to current airplane record
      $app =& Dataface_Application::getInstance();
      $airplaneRecord =& $app->getRecord();

      $fcres = mysql_query("SELECT COUNT(id) AS numflights,
                            SUM(flights.day + flights.night) AS total
                            FROM flights 
                            WHERE flights.aircraftid = ".$airplaneRecord->val('id'),$app->db());
      if(!$fcres)
      {
         echo "Nothing found in SQL query...";
      }
      //process result 
      $row = mysql_fetch_assoc($fcres);
      echo "Number of Flights: ";
      echo $row['numflights']."<br>";

      echo "Time in Aircraft: ";
      echo $row['total']."<br>";

      mysql_free_result($fcres);

      //Now extract the total number of hours for this aircraft


      //Make sure that there is a airplane record currently selected
      // (e.g. we may be in list mode or there may be no
      // Airplane records in the found set.
      if ( !isset($airplaneRecord) ) return false;
   
      // Now we show the flights
      echo "<ul>";
      foreach ( $airplaneRecord->getRelatedRecordObjects('flights') as $flight)
      {
         echo "<li>".$flight->htmlValue('date')."</li>";
      }
      echo "</ul></div>";
   }
}
?>