<?php
//non-dynamic data delcared in global scope. This is picked up later
//in a php block embedded into javascript
//$dataset1 = array(array("label"=>"c120","data"=>"1"),
//                  array("label"=>"c150","data"=>"10"),
//                  array("label"=>"camp","data"=>"7"));

class actions_time_by_year
{
  function handle(&$params)
  {
    $this->app =& Dataface_Application::getInstance();
    $body = "<br /><br />";
    import( 'Dataface/RecordGrid.php');

    //The Query, this works nicely and feeds the grid
    $result = mysql_query("SELECT YEAR(date) as year, 
                           COUNT(id) as flights,
                           SUM(day + night) AS hours
                           FROM flights 
                           GROUP BY YEAR(date)",
                            $this->app->db());

    if(!$result)
    {
       $body .= "MySQL Error...";
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //append data for creating the pie chart, again, this is not hitting
          //the global instance of $dataset1 which is what is being plotted
          $dataset1[] = array((int)$row['year']-.5, (float)$row['hours']);
       }
       mysql_free_result($result); //Frees the result after finished using it
       
       // Create new RecordGrid with the data, works nicely
       $grid = new Dataface_RecordGrid($data);  
       $body .= $grid->toHTML();   // Get the HTML of the RecordGrid

       //diagnostic dump to see what we've got
       //var_dump($dataset1);
       var_dump(json_encode($dataset1));

       //reserve space for the pie chart
       $body .= "<div id=\"typeBarChart\"
                      style=\"width:600px;height:300px;\"><div>";
    }

    // Show the built-up content in a template derived from the main Template
    df_display(array('body' => $body,
                     'dataset1' => json_encode($dataset1)),
                      'barChart.html');
  }
}
?>

