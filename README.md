# Codiad-LDAPExternalAuth
LDAP External Authentication Drop-In for Codiad

Written by Korynkai (Matt Schultz) of QuantuMatriX Technologies.

## Installation

* Download `ldap.php`.
* Edit `ldap.php` in a text editor, changing configuration values as needed. Do not edit the core logic (anything under the "Do not edit anything under..." line) -- you can break functionality, corrupt your users.php file, or even accidentally allow anybody to log in and modify your code. Only edit under the line if you're looking to experiment and have a test environment set up.
* Save `ldap.php` somewhere on the webserver, preferably somewhere within the Codiad root (I created a special directory for External Authentication called `auth` on my setup) and ensure your webserver daemon has permissions to read the file.
* Edit Codiad's `config.php` in a text editor, uncommenting and/or adding the line `define("AUTH_PATH", "/path/to/ldap.php");`. Replace "/path/to" with the actual path. You may use the `BASE_PATH` directive if you saved `ldap.php` to somewhere within the Codiad root. For example, on my setup (with the `auth` directory), this is set to `define("AUTH_PATH", BASE_PATH . "/auth/ldap.php");`

## Configuration

Most of the configuration should be completed during the installation; However it would be wise to explain these values:

* `$server` would be your LDAP server's connection URI; For example:
 * `$server = 'ldap://ldap.example.com:389';`

* `$basedn` would be your LDAP server's search base distinguished name. This would be where Codiad looks for user entries within LDAP. Example:
 * `$basedn = 'ou=people,dc=example,dc=com';`

* `$filter` is your LDAP user search filter. This tells Codiad which attribute/value pairs to look for as the username to look up. If you aren't sure what to do here, you may use one of the alternatives or use the references either at http://tools.ietf.org/search/rfc4515 (quite technical IETF RFC) or http://goo.gl/FOdGp7 (CentOS documentation page on LDAP search filters). The variable `$1` must always be supplied as a value as it signifies the username. The default will allow a CN or an email to log in; however, the user environments between the CN and email logins would differ, essentially acting as separate users within Codiad. Examples:
 * `$filter = '(&(objectClass=*)(|(cn=$1)(email=$1)))';` <-- Allows CN or email to denote the username. As it uses a logical `or` (`|`), it allows more than one field to directly act as the username, in effect allowing each LDAP user (with both a CN and an email attribute) to create/log-in to two Codiad users if they so desire.
 * `$filter = '(&(objectClass=*)(cn=$1))';` <-- Strictly use CN as username.
 * `$filter = '(&(objectClass=*)(email=$1))';` <-- Strictly use email as username.
 * `$filter = '(&(objectClass=*)(uniqueIdentifier=$1))';` <-- Strictly use uniqueIdentifier as username. This is useful for custom self-identifiable usernames and is the filter we use on our setup, however it may require additional configuration on LDAP.

* `$createuser` either allows or denies the automatic creation of a Codiad user upon successful LDAP authentication. If set to true, a `user` will be created if the user successfully authenticates through LDAP but is not present within Codiad's `data/users.php` file. If set to `false`, the user will be denied access if they are not present within Codiad's `data/users.php` file, regardless of whether or not the user has successfully authenticated to LDAP. Default is `true`.

* `$version` -- The LDAP protocol version used by the LDAP server. Should not be changed unless you are sure you are using a different version of the protocol. _Should not be confused with any specific LDAP server version._ The developer discourages modifying this value.

