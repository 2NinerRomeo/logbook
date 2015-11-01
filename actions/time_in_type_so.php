    <?php
    //non-dynamic data delcared in global scope. This is picked up later
    //in a php block embedded into javascript
    $dataset1 = array(array("label"=>"c120","data"=>"1"),
                      array("label"=>"c150","data"=>"10"),
                      array("label"=>"camp","data"=>"7"));
    class actions_time_in_type
    {
      function handle(&$params)
      {
        $this->app =& Dataface_Application::getInstance(); 
        //The Query
        $result = mysql_query("SELECT typeDes, total
                               FROM myTable", $this->app->db());
        //reserch leads me to believe that this *should* make all subsequent
        //references to $dataset1 use the global instance
        global $dataset1;
        //experimenting with appending more non-dynamic data
        //for some reason, this syntax does not seem to touch $dataset1 
        array_push($dataset1, array("label"=>"dv20","data"=>"1"));
        //This syntax is working, but $dataset1 is not the same as the global 
        //$dataset1. Prepending "global" here seems to crash the script
        $dataset1[] = array("label"=>"pa18","data"=>"5");
        while($row = mysql_fetch_assoc($result))
        {
           //append data to the array, again, this is not hitting
           //the global instance of $dataset1
           $dataset1[] = array("label"=>$row['typedes'],"data"=>$row['total']);
        }
        mysql_free_result($result); //Frees the result after finished using it           
        //diagnostic dump to see what we've got
        //This shows that we've constructed the dynamic data set, but it
        //seems to be scoped only to this function and does not make it into
        //javascript.
        var_dump($dataset1);
      }
    }
    ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
    <script type="text/javascript">
    $(function () {
       //This is getting only what was done original init of $dataset1, nothing that
       //happened in the function made a difference
       var dataset1 = <?php echo json_encode($dataset1); ?>;
    });
    </script>