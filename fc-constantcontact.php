<?php
require 'class.rc4crypt.php';
require 'class.xmlparser.php';
include 'ConstantContactUtils.php';

$ConstantContact_Auth = array(
    'user'  => 'user',    // Replace "username" with your Constant Contact username
    'pass'  => 'password' // Replace "password" with your Constant Contact password
);

$ListName = 'My Awesome List!'; // The exact name of your list in Constant Contact.

/**
 * Use a custom field during checkout?  If true, check for the presence of
 * $Custom_Field below. If false, always subscribe the customer.  Use wisely.
 */
$Use_Custom_Field = true;

$Custom_Field = 'Subscribe';    // Name of the custom "Opt In" field during checkout.
$Custom_Field_Value = 'yes';    // The value of the custom field that indicates the customer's agreement.

$key = 'CHANGE THIS TEXT to your own datafeed keyphrase';

isset($_POST['FoxyData']) or die("error"); // Make sure we got passed some FoxyData

function fatal_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
	die($errstr);
	return true;
}
set_error_handler('fatal_error_handler');

$FoxyData = rc4crypt::decrypt($key, urldecode($_POST["FoxyData"]));

$data = new XMLParser($FoxyData);   // Parse that XML.
$data->Parse();

foreach ($data->document->transactions[0]->transaction as $tx) {
  $subscribe = !$Use_Custom_Field;

  if ($Use_Custom_Field) {
    foreach ($tx->custom_fields[0]->custom_field as $field) {
      $subscribe = $subscribe ||
       ($field->custom_field_name[0]->tagData == $Custom_Field &&
       $field->custom_field_value[0]->tagData == $Custom_Field_Value);
    }
  }

  if ($subscribe) {
    subscribe_user_to_list(// See MailChimpUtils.php for documentation.
     array( 
     'first_name' => $tx->customer_first_name[0]->tagData,
     'last_name' => $tx->customer_last_name[0]->tagData,
     'email' => $tx->customer_email[0]->tagData),
     $ListName,
     $ConstantContact_Auth);
  }
}

print "foxy";

?>
