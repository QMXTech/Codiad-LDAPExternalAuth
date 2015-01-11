<?php
/////////////////////////////////////////////////////////////////////////////
// Codiad LDAP External Authentication					   //
//									   //
// Written by Korynkai of QuantuMatriX Technologies.			   //
//									   //
//WHEN INSTALLED, PLEASE ENSURE ldap.php IS ONLY READABLE BY THE WEBSERVER!//
//									   //
// FOR EXOTIC INSTALLATIONS, IT MAY BE ADVISED TO MOVE THE SERVER	   //
// INFORMATION TO A LOCATION OUTSIDE THE WEBSERVER ROOT, AND USE	   //
// 'require_once' IN ldap.php (See PHP docs for details). DON'T FORGET TO  //
// ENCLOSE THE SERVER INFORMATION WITHIN THE NEW FILE IN PHP TAGS AND 	   //
// ENSURE ONLY THE WEBSERVER CAN READ THE NEW FILE!			   //
//									   //
// FAILING TO DO SO CAN RESULT IN SEVERE CONSEQUENCES IF YOUR LDAP SERVER  //
// IS EXPOSED IN ANY WAY AND CAN GIVE YOUR PUPPY A HORRIBLE VIOLENT DEATH! //
//									   //
// YOU HAVE BEEN WARNED!						   //
//									   //
// Author's notes:							   //
// As this is more of a configuration / authentication drop-in, I see no   //
// reason to create a separate license file. This is really just for	   //
// anybody looking for something like this, and I don't see why Codiad	   //
// shouldn't have it as an option anyway as LDAP authentication is	   //
// generally common among groups running such servers anyway.		   //
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

////////////////////////
// Server information //
////////////////////////

// The LDAP version to use (should normally be '3')
    $version = 3;

// The LDAP connection URI of the server.
    $server = 'ldap://ldap.example.com:389';

// The optional DN to bind to, if any.
//    $binddn = 'cn=codiad,ou=services,dc=example,dc=com';

// The optional password of the bind context, if any.
//    $bindpw = 'secret';

// The DN to search under on the server.
    $basedn = 'ou=people,dc=example,dc=com';

// The search filter. If you aren't sure what this is, this seems to be a good
// reference: 
//   http://www.ldapexplorer.com/en/manual/109010000-ldap-filter-syntax.htm
// Default is: '(&(objectClass=*)(|(cn=$1)(email=$1)))'.
    $filter = '(&(objectClass=*)(|(cn=$1)(email=$1)))';

// The user password attribute. Default is: 'userPassword'.
    $pwattr = 'userPassword';

/////////////////////////////////////////////////////////////////////////////
// Do not edit anything under this line unless you know what you're doing! //
/////////////////////////////////////////////////////////////////////////////

    if ( !isset( $_SESSION['user'] ) ) {

    	if( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {

	    $username = $_POST['username'];
	    $password = $_POST['password'];

	    $tfilter = str_replace( '$1', $username, $filter );
	    $socket = ldap_connect( $server );

	    ldap_set_option( $socket, LDAP_OPT_PROTOCOL_VERSION, $version );
	    ldap_set_option( $socket, LDAP_OPT_REFERRALS, 0 );

   	    if ( $socket ) {

		if ( isset( $binddn ) && isset( $bindpw ) ) {

		    $bind = ldap_bind( $socket, $binddn, $bindpw );

		} elseif ( isset( $binddn ) ) {

		    $bind = ldap_bind( $socket, $binddn );

		} else {

		    $bind = ldap_bind( $socket );

		}

	    	if ( $bind ) {

	            $result = ldap_search( $socket, $basedn, $tfilter );
                    $count  = ldap_count_entries( $socket, $result );

                    if ( $count == 1 ) {

                    	$data = ldap_get_entries( $socket, $result );             
                    	$auth = ldap_compare( $socket, $data[0]['dn'], $pwattr, $password);

                    	if ( ! $auth ) {

                            die( formatJSEND( "error", "Password does not match." . $pwattr ) );

                    	} else {

		            $_SESSION['user'] = $username;

			    if (isset($_POST['language'])) {

		                $_SESSION['lang'] = $_POST['language'];

			    } else {

			        $_SESSION['lang'] = 'en';

			    }

		            $_SESSION['theme'] = $_POST['theme'];
		            $_SESSION['project'] = $_POST['project'];

			    echo formatJSEND("success", array("username"=>$_SESSION['user']));

			    header('Location: '.$_SERVER['PHP_SELF'].'?action=verify');

		        }

		    } elseif ( $count > 1 ) {

		        die( formatJSEND( "error", "A server error occurred: LDAP filter is non-unique. Please ensure this is a unique identifier within its context.\n
						        If the problem persists, please contact the webmaster. If you are the webmaster, please check the LDAP filter used." ) );

		    } else {

		        die( formatJSEND( "error", "LDAP user $username does not exist." ) );

		    }

	        } else {

		    die( formatJSEND( "error", "An error occurred: Cannot bind to LDAP server. Please contact the webmaster.\n
			 			    If you are the webmaster, please ensure the bind DN is accurate and the DN exists and is bindable from this webserver." ) );

	    	}

	    } else {

	    	die( formatJSEND( "error", "An error occurred: Cannot connect to LDAP server. Please contact the webmaster.\n
					        If you are the webmaster, please contact your LDAP server administrator or check if your LDAP server is running." ) );

	    }

        }

    }

?>
