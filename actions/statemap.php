<?php
class actions_statemap
{
  function handle(&$params)
  {
    /////////////////
    //Beginning Stuff
    /////////////////
    $this->app =& Dataface_Application::getInstance();
    $body = "<br /><br />";
    $statesJson = "";
    import( 'Dataface/RecordGrid.php');

    $body .= "<br/>\n <h2>States Landed In</h2>";
    $body .= "<br/>\n";
    //reserve space for the Map
    $body .= "<div id=\"mapdiv\" ";
    $body .= "style=\"width: 750px; height: 450px;\"></div>\n";

    ///////////////////////////////////////
    //Query States of Midflight Landings //
    ///////////////////////////////////////
    $midflight = mysql_query("SELECT lc.state as state,
                              COUNT(*) AS stLnd
                              FROM locations AS lc 
                              INNER JOIN flightWaypoints AS fwp
                              ON fwp.locationID = lc.id 
                              WHERE fwp.landing = 1
                              GROUP BY lc.state 
                              ORDER BY lc.state DESC",
                              $this->app->db());
    if(!$midflight)
    {
       $body .= "Query Error: Mid Flight Landings";
       $body .= mysql_error();
    }


    /////////////////////////////////////////
    //Query States of Destination airports //
    /////////////////////////////////////////
    $result = mysql_query("SELECT lc.state,
                           COUNT(*) AS stFlt,
                           SUM(fl.landings) AS stLnd
                           FROM flights AS fl 
                           INNER JOIN locations AS lc 
                           ON fl.landingPtId = lc.id 
                           WHERE fl.landings > 0
                           GROUP BY lc.state 
                           ORDER BY stFlt DESC",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Destination States";
       $body .= mysql_error();
    }
    else
    {
       //copy rows into an array
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
         $destinationData[] = $row;
       }
       mysql_free_result($result); 
    }

    //Merge midflight data with the destination data
    if($midflight)
    {
       //Iterate the midflight data
       while($midRow = mysql_fetch_assoc($midflight))
       {
          //$body .= "\n <br> Midflight state ";
          //$body .= $midRow['state'];
          $foundState = False;
          foreach($destinationData as &$stateRow)
          {
             if($stateRow['state'] == $midRow['state'])
             {
                //$body .= "\n <br> &nbsp Landings before: ";
                //$body .= $stateRow['stLnd'];
                $foundState = True;
                $stateRow['stLnd'] += $midRow['stLnd'];
                //$body .= "\n <br>  &nbsp Landings after: ";
                //$body .= $stateRow['stLnd'];
             }
          }
          if(!$foundState)
          {
            //Add this state to the end of the list
            //$body .= "\n <br> &nbsp State not found, appending. ";
            array_push($destinationData,
                       array("state" => $midRow['state'],
                             "stFlt" => 0,
                             "stLnd" => $midRow['stLnd']));
          }
       }
    }
    

    foreach($destinationData as $combinedRow)
    {
          $statesJson .= "{\n \"id\":\"US-";
          $statesJson .= $combinedRow['state'];
          $statesJson .= "\",\n\"showAsSelected\":true\n},";
    }
    $grid = new Dataface_RecordGrid($destinationData);  
    $body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    /////////////////
    //Finishing Stuff
    /////////////////

    //Here are some extra notes
    $body .= "<br/>\n Landing counts for each state may be inaccurate;";
    $body .= "Total landings for a flight are counted and added to the ";
    $body .= "state of the destination";

    // Show the built-up content in a template derived from the main
    // Template
    df_display(array('body' => $body,
                     'statesJson' => $statesJson,
                     ),
               'states.html');
  }
}
?>

