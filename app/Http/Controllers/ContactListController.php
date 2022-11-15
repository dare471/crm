<?php

namespace App\Http\Controllers;

use App\Models\Parser;
use Illuminate\Http\Request;

class ContactListController extends Controller
{
    public function user_photo($iin)
    {
        $dir    = '/var/www/html/users-photo/';
        $files1 = scandir($dir);
        $files2 = scandir($dir, 1);
        foreach ($files1 as $key => $value) {
            $n_photo=substr($value, 0, 12);
            if ($n_photo==$iin) {
                echo "<img src='http://192.168.1.16:8081/users-photo/$n_photo.png' width='20%'><br/>";
                echo "<a href='http://192.168.1.16:8081/users-photo/$n_photo.png' download>Скачать файл</a>";        
                $ldap_password = 'VBfgRT876';
                $ldap_username = 'd.onglassyn@alagro.local';
                $ldap_connection = ldap_connect("AASRVV01.alagro.local");
                
                if (FALSE === $ldap_connection) {
                    // Uh-oh, something is wrong...
                    echo 'Unable to connect to the ldap server';
                }
                
                // We have to set this option for the version of Active Directory we are using.
                ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
                ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
                
                if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap_password)) {
                
                    //Your domains DN to query
                    $ldap_base_dn = 'DC=alagro,DC=local';
                
                   //Get standard users and contacts
    $search_filter = '(|(objectCategory=person)(objectCategory=contact))';
 
    //Connect to LDAP
    $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);
 
    if (FALSE !== $result) {
        $entries = ldap_get_entries($ldap_connection, $result);
 
        // Uncomment the below if you want to write all entries to debug somethingthing 
        //var_dump($entries);
 
        //Create a table to display the output 
        echo '<h2>AD User Results</h2></br>';
        echo '<table border = "1"><tr bgcolor="#cccccc"><td>Username</td><td>Last Name</td><td>First Name</td><td>E-Mail Address</td></tr>';
 
        //For each account returned by the search
        for ($x = 0; $x < $entries['count']; $x++) {
            $LDAP_samaccountname = "";
            if (!empty($entries[$x]['samaccountname'][0])) {
                $LDAP_samaccountname = $entries[$x]['samaccountname'][0];
                if ($LDAP_samaccountname == "NULL") {
                    $LDAP_samaccountname = "";
                }
            } else {
                //#There is no samaccountname s0 assume this is an AD contact record so generate a unique username
 
                $LDAP_uSNCreated = $entries[$x]['usncreated'][0];
                $LDAP_samaccountname = "CONTACT_" . $LDAP_uSNCreated;
            }
 
            //Last Name
            $LDAP_LastName = "";
 
            if (!empty($entries[$x]['sn'][0])) {
                $LDAP_LastName = $entries[$x]['sn'][0];
                if ($LDAP_LastName == "NULL") {
                    $LDAP_LastName = "";
                }
            }
 
            //First Name
            $LDAP_FirstName = "";
 
            if (!empty($entries[$x]['givenname'][0])) {
                $LDAP_FirstName = $entries[$x]['givenname'][0];
                if ($LDAP_FirstName == "NULL") {
                    $LDAP_FirstName = "";
                }
            }
            //Email address
            $LDAP_InternetAddress = "";
 
            if (!empty($entries[$x]['mail'][0])) {
                $LDAP_InternetAddress = $entries[$x]['mail'][0];
                if ($LDAP_InternetAddress == "NULL") {
                    $LDAP_InternetAddress = "";
                }
            }
               echo "<tr><td><strong>" . $LDAP_samaccountname . "</strong></td><td>" . $LDAP_LastName . "</td><td>" . $LDAP_FirstName . "</td><td>" . $LDAP_InternetAddress . "</td></tr>";
            } //END for loop
            } //END FALSE !== $result
                ldap_unbind($ldap_connection); // Clean up after ourselves.
                echo ("</table>"); //close the table
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parser  $parser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parser $parser)
    {
        //
    }
}
