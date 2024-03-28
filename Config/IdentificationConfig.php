<?php
namespace Ppci\Config;
use CodeIgniter\Config\BaseConfig;
class IdentificationConfig extends BaseConfig {
/**
 * Identification mode
 * BDD : logins in database
 * LDAP : login in LDAP xxx
 * CAS : use a CAS server
 * HEADER : use the login transmitted by the web server (identification SAML with Mellon)
 * LDAP-BDD : test first LDAP, then BDD
 * CAS-BDD : a button to use CAS identification
 *
 * @var string
 */
public $identification_type = "BDD";
/**
 * List of parameters to use SAML identification with apache2-mellon
 *
 * @var array
 */
public array $ident_header_vars = array(
	"radical" => "MELLON",
	"login" => "MELLON_MAIL",
	"mail" => "MELLON_MAIL",
	"cn" => "MELLON_CN",
	"organization" => "MELLON_SHACHOMEORGANIZATION",
	"createUser" => true
);
/**
 * Organizations granted for Header connection
 * each organization must be separated by a comma
 *
 * @var string
 */
public $organizationsGranted = "";
public $ident_header_logout_address = "";
/**
 * Attributes used to populate the login. CAS identification
 *
 * @var array
 */
public array $user_attributes = array (
	"mail" => "mail",
	"firstname"=>"givenName",
	"lastname"=>"sn",
	"name"=>"cn",
	"groups"=>"supannentiteaffectation"
);
/**
 * Parameters for LDAP identification
 *
 * @var array
 */
public array $LDAP = array(
    "address"=>"localhost",
    "port" => 389,
    "rdn" => "cn=manager,dc=example,dc=com",
    "basedn" => "ou=people,ou=example,o=societe,c=fr",
    "user_attrib" => "uid",
    "v3" => true,
    "tls" => false,
    "upn_suffix" => "", //pour Active Directory
    "groupSupport"=>false,
    "groupAttrib"=>"supannentiteaffectation",
    "commonNameAttrib"=>"displayname",
    "mailAttrib"=>"mail",
    'attributgroupname' => "cn",
    'attributloginname' => "memberuid",
    'basedngroup' => 'ou=example,o=societe,c=fr',
    "timeout"=>2,
    "ldapnoanonymous" => false,
    "ldaplogin" => "",
    "ldappassword" => ""
);
/**
 * Parameters for CAS identification
 *
 * @var array
 */
public array $CAS = array (
    "CAS_address" => "localhost",
    "CAS_uri" => "/cas",
    "CAS_port" => 443,
    "CAS_debug" => false,
    "CAS_CApath" => "",
    "CAS_get_groups" => 1,
    "CAS_group_attribute" => "supannEntiteAffectation"
);
/**
 * Number of tentatives of connection before block
 *
 * @var integer
 */
public $CONNECTION_max_attempts = 5;
/**
 * Blocking duration, in seconds. Reinitialized at each tentative
 *
 * @var integer
 */
public $CONNECTION_blocking_duration = 600;

/**
 * Time to resend an email to the administrator if an account is blocked
 *
 * @var integer
 */
public $APPLI_mailToAdminPeriod = 7200;
/**
 * Maximum period of inactivity for accessing an administration module
 */
public $APPLI_admin_ttl = 600; 
public $APP_passwordMinLength = 12;
/**
 * If set to 1, authorises recovery of a new password in the event of loss
 *
 * @var integer
 */
public $APPLI_lostPassword = 1; 
}