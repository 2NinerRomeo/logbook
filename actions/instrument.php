<?php
class actions_instrument
{
  function handle(&$params)
  {
    /////////////////
    //Beginning Stuff
    /////////////////
    $this->app =& Dataface_Application::getInstance();
    $body = "<br /><br />";
    import( 'Dataface/RecordGrid.php');

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
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //$picHours[]   = array((float)$row['total_pic'],(int)0);
	  $picHours = (float)$row['total_pic'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid

    }

    ///////////////////////
    //Query for PIC CC
    ///////////////////////
    $result = mysql_query("SELECT SUM(flights.pic)
                           AS pic_cc
                           FROM flights
                           WHERE flights.crossCountry = true", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: PIC CC Hours <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //$picCC[] = array((float)$row['pic_cc'],(int)0);
          $picCC = (float)$row['pic_cc'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid
    }

    ///////////////////////
    //  Query for hood   //
    ///////////////////////
    // This query is a little convoluted because it should exclude synthetic
    // training devices, so we check that the class is an airplane
    $result = mysql_query("SELECT SUM(flights.simulated) 
                           AS hood 
                           FROM flights
                           INNER JOIN airplanes as ap 
                           ON flights.aircraftid = ap.id 
                           INNER JOIN catclass as cl 
                           ON ap.class = cl.id 
                           INNER JOIN catclass as ct 
                           ON cl.parentID = ct.id 
                           WHERE ct.name = 'Airplane'",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Hood Hours <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //$picCC[] = array((float)$row['pic_cc'],(int)0);
          $hood = (float)$row['hood'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid
    }

    ///////////////////////
    // Query for actual  //
    ///////////////////////
    // This query is a little convoluted because it should exclude synthetic
    // training devices, so we check that the class is an airplane
    $result = mysql_query("SELECT SUM(flights.actual) 
                           AS actual 
                           FROM flights
                           INNER JOIN airplanes as ap 
                           ON flights.aircraftid = ap.id 
                           INNER JOIN catclass as cl 
                           ON ap.class = cl.id 
                           INNER JOIN catclass as ct 
                           ON cl.parentID = ct.id 
                           WHERE ct.name = 'Airplane'",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Actual IMC Hours <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $actual = (float)$row['actual'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 

    }

    ////////////////////////////////
    // Query for Ground Trainers  //
    ////////////////////////////////
    $result = mysql_query("SELECT SUM(flights.actual + flights.simulated) 
                           AS gTrainer
                           FROM flights
                           INNER JOIN airplanes as ap 
                           ON flights.aircraftid = ap.id 
                           INNER JOIN catclass as cl 
                           ON ap.class = cl.id 
                           INNER JOIN catclass as ct 
                           ON cl.parentID = ct.id 
                           WHERE ct.name = 'Simulator'
                           OR cl.name = 'Simulator'
                           OR ct.name = 'Flight Training Device'
                           OR cl.name = 'Flight Training Device'
                           OR ct.name = 'Aviation Training Device'
                           OR cl.name = 'Aviation Training Device'",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Ground Trainer Hours <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $gTrainer = (float)$row['gTrainer'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }


    ///////////////////////////////////////
    // Query for Instrument Instruction  //
    ///////////////////////////////////////
    $result = mysql_query("SELECT SUM(flights.simulated + flights.actual) 
                           AS dualInst
                           FROM flights
                           WHERE flights.dual_rx != 0.0
                           AND flights.dual_rx IS NOT NULL",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Dual Instrument <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $dualInst = (float)$row['dualInst'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }

    //////////////////////////////////////////////////
    // Query for Instrument Instruction (sixty-day) //
    //////////////////////////////////////////////////
    $result = mysql_query("SELECT SUM(flights.simulated + flights.actual) 
                           AS sixtyDay
                           FROM flights
                           WHERE (flights.dual_rx != 0.0
                           OR flights.dual_rx IS NOT NULL)
                           AND flights.date BETWEEN
                           (CURDATE() - INTERVAL 60 DAY) AND
                           CURDATE()",
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Dual Instrument <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $sixtyDay = (float)$row['sixtyDay'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }

    //////////////////////////////////////////
    //Calculation of instrument requirement //
    //////////////////////////////////////////
    
    $gTrainerAllowed = min($gTrainer, 20.0);
    $instReq = $hood + $actual + $gTrainerAllowed;


    /////////////////
    //Finishing Stuff
    /////////////////
    $body .= "<br/>\n <h2>Instrument Rating</h2>";
    $body .= "<br/>\n Instrument requirments are in FAR 61.65";
    $body .= "<br/>\n";
    //reserve space for the bar chart
    $body .= "<div id=\"combinedChart\" style=\"width:800px; ";
    $body .= "height:612px;\"></div>\n";
    //Here are some extra notes
    $body .= "<br/>\n Hood, Actual and Simulator may all contribute to the Instrument experience requirement, however Simulators may only contribute 20 hours toward that requirement (for non 142 training, FAR 61.65(e)(2))";
    $body .= "<br/>\n Dual CC, 250nm Airways/ATC Routing, approaches at each airport, 3 kinds of approaches with Nav Systems";

    // Show the built-up content in a template derived from the main
    // Template
    df_display(array('body' => $body,
                     'PicCC' => $picCC,
                     'InstReq' => $instReq,
                     'HoodIMC' => $hood,
                     'ActualIMC' => $actual,
                     'Simulator' => $gTrainer,
                     'IFRInstruct' => $dualInst,
                     'SixtyDay' => $sixtyDay,
                     ),
               'instrument.html');
  }
}
?>
