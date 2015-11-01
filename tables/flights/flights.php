<?php
class tables_flights
{
function getTitle(&$record)
   {
//Something went horribly wrong with one of these title schemes
//      return $record->val(date). " ". $record->val(aircraft); //gives "array"
// It would be neat to title with the date and/or aircraft as well
//      return string date($record->val(date));

	return 'Flight '. $record->val(id);

   }

function titleColumn()
   {
      return date;
   }
}
?>