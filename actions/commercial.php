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

    //////////////////////////
    //Query for total hours //
    //////////////////////////
    $result = mysql_query("SELECT SUM(flights.day + flights.night) 
                           AS total_hours
                           FROM flights", 
                            $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Total Hours";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //$totalHours[] = array((float)$row['total_hours'],(int)0);
          $totalHours = (float)$row['total_hours'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid
    }

    //////////////////////////////////
    //Query for total powered hours //
    //////////////////////////////////
    $result = mysql_query("SELECT SUM(flights.day + flights.night) 
                           AS total_powered
                           FROM flights
                           INNER JOIN airplanes
                           ON flights.aircraftid = airplanes.id
                           WHERE airplanes.EngineID IS NOT NULL", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Total Powered Hours";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          //$totalHours[] = array((float)$row['total_hours'],(int)0);
          $totalPoweredHours = (float)$row['total_powered'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid
    }

    ////////////////////////////////////
    //Query for total Airplanes Hours //
    ////////////////////////////////////
    $result = mysql_query("SELECT SUM(flights.day + flights.night) 
                           AS total_airplanes FROM flights
                           INNER JOIN airplanes 
                           ON flights.aircraftid = airplanes.id 
                           INNER JOIN catclass AS class 
                           ON airplanes.class = class.id 
                           INNER JOIN catclass AS cat 
                           ON class.parentid = cat.id 
                           WHERE cat.name = 'Airplane'", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Total Airplane Hours";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $totalAirplaneHours = (float)$row['total_airplanes'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }


    ////////////////////////
    //Query for PIC hours //
    ////////////////////////
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
          $picHours = (float)$row['total_pic'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
       //// Create new RecordGrid with the data, works nicely
       //$grid = new Dataface_RecordGrid($data);  
       //$body .= $grid->toHTML();   // Get the HTML of the RecordGrid
    }

    /////////////////////
    //Query for PIC CC //
    /////////////////////
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

    ///////////////////////////
    //Query for Dual Complex //
    ///////////////////////////
    $result = mysql_query("SELECT SUM(flights.dual_rx)
                           AS DualComplex
                           FROM flights
                           INNER JOIN airplanes
                           ON flights.aircraftid
                           = airplanes.id
                           WHERE airplanes.retract = 1
                           AND ( flights.dual_rx IS NOT NULL
                           OR flights.dual_rx <> 0.0)", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Dual Complex Hours <br>";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $dualComplex = (float)$row['DualComplex'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }

    /////////////////////////
    //Query for Solo Hours //
    /////////////////////////
    $result = mysql_query("SELECT SUM(flights.day + flights.night) 
                           AS solo FROM flights
                           WHERE solo = 1", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Total Solo Hours";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $solo = (float)$row['solo'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }


    ///////////////////////////////
    //Query for Night Solo Hours //
    ///////////////////////////////
    $result = mysql_query("SELECT SUM(flights.night) 
                           AS soloNight FROM flights
                           WHERE solo = 1", 
                           $this->app->db());
    if(!$result)
    {
       $body .= "Query Error: Night Solo Hours";
       $body .= mysql_error();
    }
    else
    {
       while($row = mysql_fetch_assoc($result))  //Fetch all rows
       {
          //append data to array building the table
          $data[] = $row;
          $soloNight = (float)$row['soloNight'];
       }
       //Frees the result after finished using it
       mysql_free_result($result); 
    }


    /////////////////
    //Finishing Stuff
    /////////////////
    $body .= "<br/>\n <h2>Commercial Rating</h2>";
    $body .= "<br/>\n Commercial requirments are in ";
    $body .= "<a href=\"http://rgl.faa.gov/Regulatory_and_Guidance_Library/rgFar.nsf/FARSBySectLookup/61.129\">";
    $body .= "FAR 61.129</a>";
    $body .= "<br/>\n";
    //reserve space for the bar chart
    $body .= "<br/>\n";
    $body .= "<div id=\"combinedChart\" style=\"width:800px; ";
    $body .= "height:1050px;\"></div>\n";
    //Here are some extra notes
    $body .= "<br/>\n 2hr, 100nm Dual Cross Country, Day";
    $body .= "<br/>\n 2hr, 200nm Dual Cross Country, Night";
    $body .= "<br/>\n 300nm (250nm straight line) Solo Cross Country";
    $body .= "<br/>\n Solo Night Landings with tower";

    // Show the built-up content in a template derived from the main
    // Template
    df_display(array('body' => $body,
                     'totalHours' => $totalHours,
                     'powered' => $totalPoweredHours,
                     'airplanes' => $totalAirplaneHours,
                     'picHours' => $picHours,
                     'PicCC' => $picCC,
                     'CommDual' => 0,
                     'DualSIMC' => $dualInst,
                     'DualSIMCSE' => 0,
                     'DualCplx' => $dualComplex,
                     'DualExam' => 0,
                     'Solo' => $solo,
                     'SoloNight' => $soloNight,
                     ),
               'commercial.html');
  }
}
?>

