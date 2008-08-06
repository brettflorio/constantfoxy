<?php

define('CONSTANT_CONTACT_ADD_USER_URL',
 'http://api.constantcontact.com/0.1/API_AddSiteVisitor.jsp');

/**
 * Given a user, the name of a Constant Contact list, and the CC API credentials,
 * subscribe user to named list. <b>Will die() on any errors from Constant Contact.</b>
 *
 * @param array $user	Contains the information about the user to subscribe.  Keys:
 *                     'first_name'         => string; the user's first name
 *                     'last_name'          => string; the user's last name
 *                     'email'              => string; the user's email address
 *
 * @param string $list_name     The name of the list to subscribe to.
 *
 * @param array $credentials    Contains the credentials for the CC account
 *															 that owns the list.  Keys:
 *                               'user'              => string; CC username
 *                               'pass'              => string; CC password
 *
 * @return  boolean             Returns true if member subscribed to the list.
 */
function subscribe_user_to_list($user, $list_name, $credentials) {
  $post_vars = array('loginName' => $credentials['user'],
   'loginPassword' => $credentials['pass'],
   'ea' => $user['email'],
   'ic' => $list_name,
   'First_Name' => $user['first_name'],
   'Last_Name' => $user['last_name']);
  $urlencoded = array();

  foreach ($post_vars as $key => $value)
    $urlencoded[] = urlencode($key).'='.urlencode($value);

  $post_vars = join('&', $urlencoded);

  $ch = curl_init(CONSTANT_CONTACT_ADD_USER_URL);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  list($response_code, ) = split("\n", $result);
  $response_code == '0' or die("Unable to POST new user to Constant Contact: ".$result);

  return true;    // All's well.
}
?>
