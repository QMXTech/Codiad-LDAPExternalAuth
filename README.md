# Codiad-LDAPExternalAuth
LDAP External Authentication Drop-In for Codiad

Written by Korynkai of QuantuMatriX Technologies.

WHEN INSTALLED, PLEASE ENSURE ldap.php IS ONLY READABLE BY THE WEBSERVER!

FOR EXOTIC INSTALLATIONS, IT MAY BE ADVISED TO MOVE THE SERVER
INFORMATION TO A LOCATION OUTSIDE THE WEBSERVER ROOT, AND USE
'require_once' IN ldap.php (See PHP docs for details). DON'T FORGET TO 
ENCLOSE THE SERVER INFORMATION WITHIN THE NEW FILE IN PHP TAGS AND 
ENSURE ONLY THE WEBSERVER CAN READ THE NEW FILE!

FAILING TO DO SO CAN RESULT IN SEVERE CONSEQUENCES IF YOUR LDAP SERVER 
IS EXPOSED IN ANY WAY AND CAN GIVE YOUR PUPPY A HORRIBLE VIOLENT DEATH! 

YOU HAVE BEEN WARNED!

Author's notes:
As this is more of a configuration / authentication drop-in, I see no
reason to create a separate license file. This is really just for
anybody looking for something like this, and I don't see why Codiad
shouldn't have it as an option anyway as LDAP authentication is
generally common among groups running such servers anyway.

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
