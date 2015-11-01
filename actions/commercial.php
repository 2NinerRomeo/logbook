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
       mysql_free_result($result); //Frees the result after finished using it
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    }
    /////////////////////
    //Query for PIC hours
    /////////////////////

    /////////////////
    //Finishing Stuff
    /////////////////
    //reserve space for the bar chart
    $body .= "<div id=\"totalHourChart\"
               style=\"width:600px;height:60px;\"><div>";
    // Show the built-up content in a template derived from the main Template
    df_display(array('body' => $body,
                     'totalHours' => json_encode($totalHours)),
                     'commercial.html');
  }
}
?>

