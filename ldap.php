<?php
/////////////////////////////////////////////////////////////////////////////
// Codiad LDAP External Authentication					   //
//									   //
// Written by Korynkai of QuantuMatriX Technologies.			   //
//									   //
// Author's notes:							   //
// As this is more of a configuration / authentication drop-in, I see no   //
// reason to create a separate license file.				   //
//									   //
// Permission is hereby granted, free of charge, to any person obtaining   //
// a copy of this software and associated documentation files (the	   //
// "Software"), to deal in the Software without restriction, including	   //
// without limitation the rights to use, copy, modify, merge, publish,	   //
// distribute, sublicense, and/or sell copies of the Software, and to	   //
// permit persons to whom the Software is furnished to do so, subject to   //
// the following conditions:						   //
//									   //
// The above copyright notice and this permission notice shall be	   //
// included in all copies or substantial portions of the Software.	   //
//									   //
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,	   //
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF	   //
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND		   //
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE  //
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION  //
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION   //
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.	   //
/////////////////////////////////////////////////////////////////////////////

///////////////////
// Configuration //
///////////////////

// The LDAP connection URI of the server.
    $server = "ldap://ldap.example.com:389";

// The DN to search under on the server.
    $basedn = "ou=people,dc=example,dc=com";

// The LDAP search filter. If you aren't sure what this is, the official
// IETF RFC definition (quite technical) is here: 
//	http://tools.ietf.org/search/rfc4515
// and you can find another good (easier to follow) reference here
// (shortened centos link):
//	http://goo.gl/FOdGp7
// The default will allow a CN or an email to log in (however, the user 
// environments between the CN and email logins would differ).
// Default is: '(&(objectClass=*)(|(cn=$1)(email=$1)))'.
// A couple alternatives for simple set-ups: 
//	CN only: '(&(objectClass=*)(cn=$1))'
//	email only: '(&(objectClass=*)(email=$1))'
    $filter = "(&(objectClass=*)(|(cn=$1)(email=$1)))";

// Optionally create Codiad user if it doesn't already exist. This can be set
// to 'false' if the administrator would like to manually control access to 
// Codiad from within Codiad itself, rather than let the search filter fully
// dictate user access control. 
// Default is 'true'.
    $createuser = true;

// The LDAP protocol version to use. Changing this is probably a very bad 
// idea unless you're absolutely positive you are using some completely 
// non-standard or obsolete (pre-2003) version of LDAP.
// Default is '3'. Developer heavily discourages changing this.
    $version = 3;

/////////////////////////////////////////////////////////////////////////////
// Do not edit anything under this line unless you know what you're doing! //
/////////////////////////////////////////////////////////////////////////////

    require_once( COMPONENTS . "/user/class.user.php" );

    if ( !isset( $_SESSION['user'] ) ) {

        if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {

            $User = new User();

            $User->username = $_POST['username'];
            $User->password = $_POST['password'];

            $tfilter = str_replace( "$1", $User->username, $filter );
            $socket = ldap_connect( $server );

            ldap_set_option( $socket, LDAP_OPT_PROTOCOL_VERSION, $version );
            ldap_set_option( $socket, LDAP_OPT_REFERRALS, 0 );

            if ( $socket == true ) {

                $result = ldap_search( $socket, $basedn, $tfilter );
                $count  = ldap_count_entries( $socket, $result );

                if ( $count === 1 ) {

                    $data = ldap_get_entries( $socket, $result );
                    $auth = ldap_bind( $socket, $data[0]['dn'], $User->password );

                    if ( $auth === -1 ) {

                        die( formatJSEND( "error", "An LDAP error has occurred: " . ldap_error($socket) ) );

                    } elseif ( $auth == false ) {

                        die( formatJSEND( "error", "Password does not match." ) );

                    } elseif ( $auth == true ) {

                        if ( $User->CheckDuplicate() ) {

                            if ( $createuser == true ) {

                                $User->users[] = array( 'username' => $User->username, 'password' => null, 'project' => "" );
                                saveJSON( "users.php", $User->users );
                                $_SESSION['user'] = $User->username;

                            } else {

                                die( formatJSEND( "error", "User " . $User->username . " does not exist within Codiad." ) );

                            }

                        } else {

                            $_SESSION['user'] = $User->username;

                        }

                        if ( isset( $_POST['language'] ) ) {

                            $_SESSION['lang'] = $_POST['language'];

                        } else {

                            $_SESSION['lang'] = "en";

                        }

                        $_SESSION['theme'] = $_POST['theme'];
                        $_SESSION['project'] = $_POST['project'];

                        echo formatJSEND( "success", array( 'username' => $User->username ) );
                        header( "Location: " . $_SERVER['PHP_SELF'] . "?action=verify" );

                    }

                } elseif ( $count > 1 ) {

                    die( formatJSEND( "error", "A server error occurred: LDAP filter is non-unique. Please ensure this is a unique identifier within its context.
                                                        If the problem persists, please contact the webmaster. If you are the webmaster, please check the LDAP filter used." ) );

                } else {

                    die( formatJSEND( "error", "LDAP user " . $User->username . " does not exist." ) );

                }

            } else {

                die( formatJSEND( "error", "An error occurred: Cannot connect to LDAP server. Please contact the webmaster. 
                                        If you are the webmaster, please contact your LDAP server administrator or check if your LDAP server is running." ) );

            }
	}
    }

?>
