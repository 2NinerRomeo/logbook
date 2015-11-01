<?php
class tables_instructors
{
function getTitle(&$record)
   {
      return $record->val(fname). " ". $record->val(lname);
   }

function titleColumn()
   {
      //return "CONCAT(fname, ' ', lname) ;
      return lname;
   }
}
?>