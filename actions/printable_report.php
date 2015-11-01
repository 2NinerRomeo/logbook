<?php
class actions_printable_report
{
  function handle(&$params)
  {
    echo "Pre Dataface!<br>";
    $app =& Dataface_Application::getInstance();
    $query =& $app->getQuery();
    $query['-skip'] = 0;
    $query['-limit'] = 10000;
    echo "Got App and Query!<br>";

    if ( $query['-table'] != 'airplanes' )
    {
      return PEAR::raiseError('This action can only be called on the Airplanes table.');
    }
    echo "Checked table<br>";

    $airplanes = df_get_records_array('airplanes', $query);
    echo "Hello Airplanes!";
    foreach($airplanes as $a)
    {
      echo '<table>'
          .'<tr><th>Registration</th><td>'.$a->htmlValue('registration').'</td></tr>'
          .'<tr><th>Manufacturer</th><td>'.$a->htmlValue('manufacturer').'</td></tr>'
          .'<tr><th>Model</th><td>'.$a->htmlValue('model').'</td></tr>'
          .'<tr><th>Photo</th><td>'.$a->htmlValue('picture').'</td></tr>'
          .'</table><br><br>';
          
    }
  }
}
?>