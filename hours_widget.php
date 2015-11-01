<?php
class Dataface_FormTool_hours
{
   // Builds a widget for the given record
   function buildWidget($record, &field, $form, $formFieldName, $new=false)
   {
   // Obtain an HTML_Quickform factory to create the basic elements
   $factory = Dataface_FormTool::factory();
   
   //Create a basic text field
   $el = $factory->addElement('text', $formFieldName, $widget['label']);

   return $el;
   } 
}
?>