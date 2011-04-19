<?php

error_reporting(E_ALL & ~E_DEPRECATED);

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

$group = basename(tempnam('/tmp', 'tmp_'));
$user = basename(tempnam('/tmp', 'tmp_'));
echo "Test group: $group\n";
echo "Test user: $user\n";

class GdataCliTest extends UnitTestCase {
	
	function setUp() {
		global $group, $user;
		$path = realpath(dirname(__FILE__).'/../../gdata-cli');
		$this->cmd = "$path ";
		$this->group_id = $group;
		$this->user_id = $user;
	}
	
	function tearDown() {
	}
	
	function testAuthenticate() {
	}

	function testCreateUser() {
		$user = $this->user_id;
		echo "\nTesting user.create: creating user $user...\n";
		$result = `$this->cmd user.create $user First Last k9nT4uehL`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUserInfo() {
		$user = $this->user_id;
		echo "Testing user.info...\n";
		$result = `$this->cmd user.info $user`;
		if (!preg_match("/Name: First Last/", $result)) { $this->fail(); }
		else if (!preg_match("/Quota: 25600/", $result)) { $this->fail(); }
		else if (preg_match("/Aliases:/", $result)) { $this->fail(); }
		else if (!preg_match("/User has not agreed to terms./", $result)) { $this->fail(); }
		else if (preg_match("/User is an administrator./", $result)) { $this->fail(); }
		else { $this->pass(); }
	}

	function testUserList() {
		$user = $this->user_id;
		echo "Testing user.list...\n";
		$result = `$this->cmd user.list`;
		if (!preg_match("/$user/i", $result)) { $this->fail(); }
		else { $this->pass(); }
	}

	function testUserRename() {
		$old = $this->user_id . '@test.broadinstitute.com';
		$new = $this->user_id . '@test.broadinstitute.net';
		echo "Testing user.newEmail: renaming $old to $new...\n";
		$result = `$this->cmd user.newEmail $old $new`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUserReRename() {
		$user = $this->user_id;
		$alias = strtolower("${user}@test.broadinstitute.com");
		echo "Testing user.deleteAlias: removing alias $alias...\n";
		$result = `$this->cmd -d test.broadinstitute.net user.deleteAlias ${user}@test.broadinstitute.net $alias`;
		echo "Testing user.deleteAlias: removing alias $alias a second time...\n";
		$result .= `$this->cmd -d test.broadinstitute.net user.deleteAlias ${user}@test.broadinstitute.net $alias`;
		echo "Testing user.newEmail: renaming ${user}@test.broadinstitute.net to ${user}@test.broadinstitute.com...";
		$result .= `$this->cmd user.newEmail ${user}@test.broadinstitute.net ${user}@test.broadinstitute.com`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testExpire() {
		$user = $this->user_id;
		echo "Testing user.expire: expiring user $user...\n";
		$result = `$this->cmd user.expire $user`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUnexpire() {
		$user = $this->user_id;
		echo "Testing user.unexpire: unexpiring user $user...\n";
		$result = `$this->cmd user.unexpire $user`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testImapOn() {
		$user = $this->user_id;
		echo "Testing user.imap on...\n";
		$result = `$this->cmd user.imap $user on`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testImapOff() {
		$user = $this->user_id;
		echo "Testing user.imap off...\n";
		$result = `$this->cmd user.imap $user off`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupCreate() {
		$group = $this->group_id;
		echo "Testing group.create: creating group $group...\n";
		$result = `$this->cmd group.create $group $group $group`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupAddMember() {
		$user = $this->user_id;
		$group = $this->group_id;
		echo "Testing group.addMember: adding ${user}@test.broadinstitute.com to group $group...\n";
		$result = `$this->cmd group.addMember ${user}@test.broadinstitute.com $group`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupMembers() {
		$user = $this->user_id;
		$group = $this->group_id;
		echo "Testing group.members: checking for user $user in group $group...\n";
		$result = `$this->cmd group.members $group`;
		$test = preg_match("/${user}@test.broadinstitute.com/i", $result);
		if (!$test) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupInfo() {
		$group = $this->group_id;
		$result = `$this->cmd group.info $group`;
		echo "Testing group.info...\n";
		if (!preg_match("/id: $group/i", $result)) { $this->fail(); }
		else if (!preg_match("/name: $group/", $result)) { $this->fail(); }
		else if (!preg_match("/description: $group/", $result)) { $this->fail(); }
		else if (!preg_match("/permission: Domain/", $result)) { $this->fail(); }
		else if (!preg_match("/members:/", $result)) { $this->fail(); }
		else { $this->pass(); }
	}

	function testGroupList() {
		$group = $this->group_id;
		echo "Testing group.list...\n";
		$result = `$this->cmd group.list`;
		$test = preg_match("/$group@test.broadinstitute.com/i", $result);
		if (!$test) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupRemoveMember() {
		$group = $this->group_id;
		$user = $this->user_id;
		echo "Testing group.removeMember: removing ${user}@test.broadinstitute.com from $group...\n";
		$result = `$this->cmd group.removeMember ${user}@test.broadinstitute.com $group`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testGroupDelete() {
		$group = $this->group_id;
		echo "Testing group.delete: deleting group $group...\n";
		$result = `$this->cmd group.delete $group`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUserCreateAlias() {
		$user = $this->user_id;
		$alias = "${user}testalias@test.broadinstitute.com";
		echo "Testing user.createAlias: creating alias $alias for user $user...\n";
		$result = `$this->cmd user.createAlias ${user}@test.broadinstitute.com $alias`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}
	
	function testUserAliases() {
		$user = $this->user_id;
		$alias = "${user}@test.broadinstitute.net";
		echo "Testing user.aliases: looking for alias $alias for user $user...\n";
		$result = `$this->cmd user.aliases ${user}@test.broadinstitute.org`;
		$test = strstr($result, "${user}testalias@test.broadinstitute.com");
		if ($test) { $this->fail(); } else { $this->pass(); }
	}

	function testUserCreateNickname() {
		$user = $this->user_id;
		$nickname = "${user}testnickname";
		echo "Testing user.createNickname: creating nickname $nickname for $user...\n";
		$result = `$this->cmd user.createNickname $user $nickname`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}
	
	function testUserNicknames() {
		$user = $this->user_id;
		$nickname = "${user}testnickname";
		echo "Testing user.nicknames...\n";
		$result = `$this->cmd user.nicknames $user`;
		$test = preg_match("/$nickname/i", $result);
		if (!$test) { $this->fail(); } else { $this->pass(); }
	}

	#function testNicknameList() {
	#	$user = $this->user_id;
	#	$alias = "${user}testnickalias";
	#	$result = `$this->cmd nickname.list`;
	#	echo "user: $user\nalias: $alias\nresult:\n$result\n";
	#	$test = preg_match("/$alias/i", $result);
	#	if (!$test) { $this->fail(); } else { $this->pass(); }
	#}

	function testUserDeleteAlias() {
		$user = $this->user_id;
		$alias = strtolower("${user}testalias@test.broadinstitute.com");
		echo "Testing user.deleteAlias: deleting alias $alias for user $user@test.broadinstitute.com...\n";
		$result = `$this->cmd user.deleteAlias ${user}@test.broadinstitute.com $alias`;
		$result .= `$this->cmd user.deleteAlias ${user}@test.broadinstitute.com $alias`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}
	
	function testUserDeleteNickname() {
		$user = $this->user_id;
		$nickname = "${user}testnickname";
		echo "Testing user.deleteNickname: deleting nickname $nickname for user $user...\n";
		$result = `$this->cmd user.deleteNickname $nickname`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}
	
	function testWebClipOff() {
		$user = $this->user_id;
		$result = `$this->cmd user.webclip $user off`;
		echo "Testing user.webclip off: turning off webclips for user $user...\n";
		$test = strstr($result, 'ERROR');
		if ($test) { $this->fail(); } else { $this->pass(); }
	}
	
	function testWebClipOn() {
		$user = $this->user_id;
		$result = `$this->cmd user.webclip $user on`;
		echo "Testing user.webclip on: turning on webclips for user $user...\n";
		$test = strstr($result, 'ERROR');
		if ($test) { $this->fail(); } else { $this->pass(); }
	}

	
	function testUserSuspend() {
		$user = $this->user_id;
		echo "Testing user.suspend: suspending user $user...\n";
		$result = `$this->cmd user.suspend $user`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUserRestore() {
		$user = $this->user_id;
		echo "Testing user.restore: restoring user $user...\n";
		$result = `$this->cmd user.restore $user`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testUserPassword() {
		$user = $this->user_id;
		#$pass = basename(tempnam('/tmp', 'tmp_'));
		$password = "ghi6UY8he";
		echo "Testing user.password: changing password for user $user to \"$password\"...\n";
		$result = `$this->cmd user.password $user $password`;
		if ($result) { $this->fail(); } else { $this->pass(); }
	}

	function testDeleteUser() {
		$user = $this->user_id;
		echo "Testing user.delete: deleting using $user...\n";
		$result = `$this->cmd user.delete $user`;
		if ($result) { echo "result:\n$result\n"; $this->fail(); } else { $this->pass(); }
	}


}


?>
