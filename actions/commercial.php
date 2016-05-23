<?php
class actions_commercial
{
  function handle(&$params)
  {
    /////////////////
    //Beginning Stuff
    /////////////////
    $this->app =& Dataface_Application::getInstance();
    $body = "<br /><br />";
    import( 'Dataface/RecordGrid.php');

    ///////////////////////
    //Query for total hours
    ///////////////////////
    $result = mysql_query("SELECT SUM(flights.day + flights.night) 
                           AS total_hours
                           FROM flights", 
                            $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Total Hours";
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $totalHours[] = array((float)$row['total_hours'],(int)0);
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    }
    /////////////////////
    //Query for PIC hours
    /////////////////////
    $result = mysql_query("SELECT SUM(flights.pic)
                           AS total_pic
                           FROM flights", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: PIC Hours";
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $picHours[]   = array((float)$row['total_pic'],(int)0);
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    }

    /////////////////
    //Finishing Stuff
    /////////////////
    //reserve space for the bar chart
    $body .= "<br/>\n";
    $body .= "<div id=\"totalHourChart\" style=\"width:600px; ";
    $body .= "height:60px; \"></div>\n";
    $body .= "<br/>\n";
    $body .= "<div id=\"picChart\" style=\"width:600px; ";
    $body .= "height:60px;\"></div>\n";
    $body .= "<br/>\n";
    $body .= "<div id=\"combinedChart\" style=\"width:600px; ";
    $body .= "height:150px;\"></div>\n";
    // Show the built-up content in a template derived from the main
    // Template
//    df_display(array('body' => $body,
//                     'totalHours' => json_encode($totalHours)),
//               'commercial.html');
    df_display(array('body' => $body,
                     'totalHours' => json_encode($totalHours),
                     'picHours' => json_encode($picHours)),
                     'commercial.html');
  }
}
?>

