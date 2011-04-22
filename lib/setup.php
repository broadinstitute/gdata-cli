<?php

// REFERENCES
// http://framework.zend.com/manual/en/zend.gdata.gapps.html
// http://code.google.com/googleapps/domain/gdata_provisioning_api_v2.0_reference.html
// http://code.google.com/googleapps/domain/gdata_provisioning_api_v2.0_developers_protocol.html

$path = realpath(dirname(__FILE__));
$path = "$path:$path/gapps";
set_include_path($path);
require 'functions.php';
require 'gapps/GoogleApps.php';
require 'gapps/Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Gapps');

$syntax = array(

	'group.addMember' => array(
		'argv' => array('member_id', 'group_id')
	),
	'group.create' => array(
		'argv' => array('id', 'name', 'description')
	),
	'group.delete' => array(
		'argv' => array('group_id')
	),
	'group.info' => array(
		'argv' => array('group_id')
	),
	'group.list' => array(
		'argv' => array()
	),
	'group.members' => array(
		'argv' => array('group_id')
	),
	'group.removeMember' => array(
		'argv' => array('member_id', 'group_id')
	),

	'nickname.list' => array(
		'argv' => array()
	),

	'resource.list' => array(
		'argv' => array()
	),
	'resource.listfull' => array(
		'argv' => array()
	),

	'user.create' => array(
		'argv' => array('user', 'first', 'last', 'pass')
	),
	'user.createAlias' => array(
		'argv' => array('email', 'alias')
	),
	'user.createNickname' => array(
		'argv' => array('user', 'nickname')
	),
	'user.aliases' => array(
		'argv' => array('email')
	),
	'user.delete' => array(
		'argv' => array('user')
	),
	'user.deleteAlias' => array(
		'argv' => array('email', 'alias')
	),
	'user.deleteNickname' => array(
		'argv' => array('nickname')
	),
	'user.expire' => array(
		'argv' => array('user')
	),
	'user.imap' => array(
		'argv' => array('user', 'flag')
	),
	'user.pop' => array(
		'argv' => array('user', 'flag')
	),
	'user.info' => array(
		'argv' => array('user')
	),
	'user.list' => array(
		'argv' => array()
	),
	'user.newEmail' => array(
		'argv' => array('old_email', 'new_email')
	),
	'user.nicknames' => array(
		'argv' => array('user')
	),
	'user.password' => array(
		'argv' => array('user', 'password')
	),
	'user.restore' => array(
		'argv' => array('user')
	),
	'user.suspend' => array(
		'argv' => array('user')
	),
	'user.unexpire' => array(
		'argv' => array('user')
	),
	'user.webclip' => array(
		'argv' => array('user', 'flag')
	),
	'report.accounts' => array(
	        'argv' => array('reportdate')
        ),
	'report.activity' => array(
	        'argv' => array('reportdate')
        ),
	'report.disk_space' => array(
	        'argv' => array('reportdate')
        ),
	'report.email_clients' => array(
	        'argv' => array('reportdate')
        ),
	'report.quota_limit_accounts' => array(
	        'argv' => array('reportdate')
        ),
	'report.summary' => array(
	        'argv' => array('reportdate')
        ),
	'report.suspended_accounts' => array(
	        'argv' => array('reportdate')
        ),
	'sites.list' => array(
	        'argv' => array()
        ),
    'site.acls' => array(
	        'argv' => array('sitename')
        ),
);
ksort($syntax);

$desc = '';
foreach ($syntax as $k => $v) {
	$desc .= "\t$k\n\t\t";
	foreach ($v['argv'] as $a) {
		$desc .= "$a ";
	}
	$desc .= "\n";
}

$usage = <<<USAGE
NAME
	gdata-cli - manipulate Google Apps data from the command line

SYNOPSIS
	gdata-cli [option] METHOD [PARAMS]
	
OPTIONS

	The available options are as follows:
	
	-d   Google Apps domain name
	
METHODS
$desc
	
AUTHORS
	Jeff Loiselle <jeff@broadinstitute.org>
	Andrew Teixeira <teixeira@broadinstitute.org>

USAGE;

?>
