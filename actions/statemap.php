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
       $body .= "Query Error: States";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $statesJson .= "{\n \"id\":\"US-";
          $statesJson .= $row['state'];
          $statesJson .= "\",\n\"showAsSelected\":true\n},";
          //$picHours[]   = array((float)$row['total_pic'],(int)0);
          //$picHours = (float)$row['total_pic'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       $grid = new Dataface_RecordGrid($data);  
       $body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    }


    /////////////////
    //Finishing Stuff
    /////////////////

    //Here are some extra notes
    $body .= "<br/>\n Landing counts for each state may be inaccurate; Total landings for a flight are counted and added to the state of the destination";

    // Show the built-up content in a template derived from the main
    // Template
    df_display(array('body' => $body,
                     'statesJson' => $statesJson,
                     ),
               'states.html');
  }
}
?>

