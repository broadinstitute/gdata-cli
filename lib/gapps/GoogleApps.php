<?php

class GoogleApps {

	var $client;
	var $service;
	var $domain;

	function __construct($authdomain, $email, $password, $svcname = Zend_Gdata_Gapps::AUTH_SERVICE_NAME) {
		$this->domain = $authdomain;
		$this->client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, $svcname);
		$this->service = new Zend_Gdata_Gapps($this->client, $authdomain);
	}

	function catchZendGdataGappsServiceException($e) {
		$message = $e->getMessage();
		echo "message: $message\n";
		$errors = $e->getErrors(); 
		foreach ($errors as $error) {
			$errorCode = $error->errorCode;
			$reason = $error->reason;
			$invalidInput = $error->invalidInput;
			echo "errorCode: $errorCode\nreason: $reason\ninvalidInput: $invalidInput\n";
		}
	}

	/**
	 * creates a user
	 */
	function userCreate($user, $first, $last, $pass) {
		#$user = strtolower($user);
		return $this->service->createUser($user, $first, $last, $pass);
	}

	/**
	 * creates a nickname for an existing user
	 */
	function userCreateNickname($user, $nick) {
		return $this->service->createNickname($user, $nick);
	}

	/**
	 * deletes a nickname for an existing user
	 */
	function userDeleteNickname($nick) {
		return $this->service->deleteNickname($nick);
	}

	/**
	 * returns a list of all nicknames for a user
	 */
	function userNicknameList($user) {
		if (preg_match('/@/', $user)) {
			$user = preg_replace('/@.*$/', '', $user);
		}
		$feed = $this->service->retrieveNicknames($user);
		$nicks = array();
		foreach ($feed as $nickname) {
			$nicks[] = $nickname->nickname->name;
		}
		return $nicks;
	}

	/**
	 * returns a list of all nicknames
	 */
	function nicknameList() {
		$feed = $this->service->retrieveAllNicknames();
		$nicks = array();
		foreach ($feed as $nickname) {
			$nicks[] = array($nickname->login->username, $nickname->nickname->name);
		}
		return $nicks;
	}

	/**
	 * returns a list of all resources
	 */
	function resourcesList() {
		$resources = array();
		return $resources;
	}

	/**
	 * returns a list of all users in the domain
	 */
	function userList() {
		$feed = $this->service->retrieveAllUsers();
		$users = array();
		foreach ($feed as $user) {
			$users[] = $user->login->username;
		}
		return $users;
	}

	/**
	 * returns information about a user
	 */
	function userInfo($user) {
		$info['user'] = $this->service->retrieveUser($user);
		if (!$info['user']) {
			return false;
		}
		$info['nick'] = $this->service->retrieveNicknames($user);
		return $info;
	}

	/**
	 * suspends a user
	 */
	function userSuspend($user) {
		return $this->service->suspendUser($user);
	}

	/**
	 * discontinues user suspension
	 */
	function userRestore($user) {
		return $this->service->restoreUser($user);
	}

	/**
	 * changes the password of the given user
	 */
	function userPassword($user, $password) {
		$userEntry = $this->service->retrieveUser($user);
		$userEntry->login->password = $password;
		$userEntry->login->changePasswordAtNextLogin = false;
		return $userEntry->save();
	}

	/**
	 * force user to change password at next login
	 */
	function userExpirePassword($user) {
		$userEntry = $this->service->retrieveUser($user);
		$userEntry->login->changePasswordAtNextLogin = true;
		$userEntry->save();
	}

	/**
	 * force user to change password at next login
	 */
	function userUnexpirePassword($user) {
		$userEntry = $this->service->retrieveUser($user);
		$userEntry->login->changePasswordAtNextLogin = false;
		$userEntry->save();
	}

	/**
	 * delete a user
	 * @param string username
	 */
	function userDelete($user) {
		if (empty($user)) {
			throw new Exception('Missing user');
		}
		try {
			$result = $this->service->deleteUser($user);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		return $result;
	
	}

	/**
	 * changes a user's primary email address
	 */
	function userNewEmail($old, $new) {
		if (!preg_match('/@.../', $old)) {
			echo "Warning: domain name required for old email ($old)\n";
		}
		if (!preg_match('/@.../', $new)) {
			echo "Warning: domain name required for new email ($new)\n";
		}
		list(, $old_domain) = explode('@', $old);
		$uri = "https://apps-apis.google.com/a/feeds/user/userEmail/2.0/$old_domain/$old";
		$entry = "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom'
			xmlns:apps='http://schemas.google.com/apps/2006'>
			<apps:property name='newEmail' value='$new'/>
			</atom:entry>";
		try {
	 		$result = $this->service->put($entry, $uri);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		} catch (Zend_Gdata_App_HttpException $e) {
			echo $e->getMessage() .": $uri\n";
		}
		return $result;
	}

	/**
	 * add a user alias
	 */
	function userCreateAlias($user, $alias) {
		$domain = preg_replace('/.*@/', '', $user);
		$uri = "https://apps-apis.google.com/a/feeds/alias/2.0/$domain";
		$content_type = 'application/atom+xml';
		$entry = "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom'
			xmlns:apps='http://schemas.google.com/apps/2006'>
			<apps:property name='aliasEmail' value='$alias' />
			<apps:property name='userEmail' value='$user' />
			</atom:entry>";
		return $this->service->post($entry, $uri, NULL, $content_type);
	}

	/**
	 * remove a user alias
	 */
	function userDeleteAlias($user, $alias) {
		$domain = preg_replace('/.*@/', '', $user);
		$uri = "https://apps-apis.google.com/a/feeds/alias/2.0/{$domain}/{$alias}";
		return $this->service->delete($uri);
	}


	/**
	 * returns all aliases for an email address
	 */
	function userAliases($email) {
		$uri = "https://apps-apis.google.com/a/feeds/alias/2.0/{$this->domain}?userEmail={$email}";
		$result = $this->service->get($uri);
		$body = $result->getBody();
		$xml = simplexml_load_string($body);
		$aliases = array();
		foreach ($xml->entry as $e) {
			$x = $e->children('http://schemas.google.com/apps/2006');
			foreach ($x->property as $p) {
				$attr = $p->attributes();
				if ($attr['name'] == 'aliasEmail') {
					$aliases[] = (string) $attr['value'];
				}
			}
		}
		sort($aliases);
		return $aliases;
	}

	/**
	 * enable/disable imap access
	 */
	function imap($user, $enable = '') {
		if ($enable == 'on') {
			$enable = 'true';
		} else {
			$enable = 'false';
		}
		$uri = "https://apps-apis.google.com/a/feeds/emailsettings/2.0/{$this->domain}/{$user}/imap";
		$entry = "<?xml version='1.0' encoding='utf-8'?>
		<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>
		    <apps:property name='enable' value='$enable' />
		</atom:entry>";
		$result = $this->service->put($entry, $uri);
		return $result;
	}

	/**
	 * enable/disable pop access
	 */
	function pop($user, $enable = '') {
		if ($enable == 'on') {
			$enable = 'true';
		} else {
			$enable = 'false';
		}
		$uri = "https://apps-apis.google.com/a/feeds/emailsettings/2.0/{$this->domain}/{$user}/pop";
		$entry = "<?xml version='1.0' encoding='utf-8'?>
		<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>
		    <apps:property name='enable' value='$enable' />
		    <apps:property name='enableFor' value='ALL_MAIL' />
		    <apps:property name='action' value='KEEP' />
		</atom:entry>";
		$result = $this->service->put($entry, $uri);
		return $result;
	}

	/**
	 * enable/disable web clips
	 */
	function webclip($user, $enable = '') {
		if ($enable == 'on') {
			$enable = 'true';
		} else {
			$enable = 'false';
		}
		$uri = "https://apps-apis.google.com/a/feeds/emailsettings/2.0/{$this->domain}/$user/webclip";
		$entry = "<?xml version='1.0' encoding='utf-8'?>
		<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>
		    <apps:property name='enable' value='$enable' />
		</atom:entry>";
		return $this->service->put($entry, $uri);
	}

	/**
	 * displays all information about a group
	 */
	function groupInfo($group_id) {
		try {
			$entry = $this->service->retrieveGroup($group_id);
		} catch (Zend_Gdata_App_InvalidArgumentException $e) {
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}

		$group = array();
		if (is_array($entry->property)) {
		    foreach ($entry->property as $p) {
			$group[$p->name] = $p->value;
		    }
		}
		return $group;
	}

	/**
	 * returns list of groups in a domain
	 */
	function groupList() {
		$feed = $this->service->retrieveAllGroups();
		$groups = array();
		foreach ($feed->entry as $entry) {
			foreach ($entry->property as $p) {
				if ($p->name == 'groupId') {
					$groups[] = $p->value;
				}
			}
		}
		return $groups;
	}

	/**
	 * returns email addresses of group members
	 */
	function groupMembers($id) {
		try {
			$feed = $this->service->retrieveAllMembers($id);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		$users = array();
		if (is_object($feed)) {
		    foreach ($feed as $user) {
			foreach ($user->property as $p) {
				if ($p->name == 'memberId') {
					$users[] = $p->value;
				}
			}
		    }
		}
		return $users;
	}

	/**
	 * creates a Google Group
	 */
	function groupCreate($id, $name, $description) {
		try {
			$result = $this->service->createGroup($id, $name, $description);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		return $result;
	}

	/**
	 * delete Google group
	 */
	function groupDelete($id) {
		try {
			$result = $this->service->deleteGroup($id);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		return $result;
	}

	/**
	 * adds member to Google Group
	 */
	function groupAddMember($email, $id) {
		try {
			$result = $this->service->addMemberToGroup($email, $id);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		return $result;
	}

	/**
	 * removes a member from Google Group
	 */
	function groupRemoveMember($user, $group_id) {
		try {
			$result = $this->service->removeMemberFromGroup($user, $group_id);
		} catch (Zend_Gdata_Gapps_ServiceException $e) {
			$this->catchZendGdataGappsServiceException($e);
		}
		return $result;
	}

	/**
	 * returns a list of resource calendars in a domain
	 */
	function resourceList() {
		$resources = $this->getResources();
		return $resources;
	}

	/**
	 * retrieves a list of resource calendars in a domain
	 */
	function getResources() {
		$domain = $this->domain;
		$uri = "https://apps-apis.google.com/a/feeds/calendar/resource/2.0/$domain/";
		list($resources, $next) = $this->getResourcePage($uri);
		while ($next) {
			list($nextresources, $next) = $this->getResourcePage($next);
			if (is_array($nextresources)) {
				$resources = array_merge($resources, $nextresources);
			}
		}
		ksort($resources);
		return $resources;
	}

	/**
	 * returns a page of resource calendars in a domain
	 */
	function getResourcePage($uri) {
		$result = $this->service->get("$uri");
		$body = $result->getBody();
		$xml = simplexml_load_string($body);

		if (preg_match('/start=/', $xml->link['href'])) {
			$next = $xml->link['href'];
		} else { $next = ""; }

		$aliases = array();
		$resources = array();

		foreach ($xml->entry as $e) {
			$x = $e->children('http://schemas.google.com/apps/2006');
			$resource = array();
			foreach ($x->property as $p) {
				$attr = $p->attributes();
				$name = $attr['name'];
				$value = $attr['value'];
				$resource["$name"] = $value;
			}
			$resourceCommonName = $resource['resourceCommonName'];
			$resources["$resourceCommonName"] = $resource;
		}
		return array($resources, $next);
	}

	public function getReport($rName = NULL, $date = NULL) {
	  $uri = 'https://www.google.com/hosted/services/v1.0/reports/ReportingData';

	  if ($rName == NULL) {
	    throw new Exception ('Report name was not provided');
	  }

	  if ($date == NULL) {
	    throw new Exception ('Date was not provided');
	  }

	  $rType = 'daily';
	  $rDomain = $this->domain;
	  $rPage = 1;

	  $goog_resp = NULL;
	  $resp = $this->client->getLastResponse();
	  // This loop shamelessly stolen from the Zend API
	  foreach (explode("\n", $resp->getBody()) as $l) {
            $l = chop($l);
            if ($l) {
                list($key, $val) = explode('=', chop($l), 2);
                $goog_resp[$key] = $val;
            }
	  }

	  $rToken = $goog_resp['SID'];

	  $xml = '<?xml version="1.0" encoding="UTF-8"?>
<rest xmlns="google:accounts:rest:protocol"
    xmlns:xsi=" http://www.w3.org/2001/XMLSchema-instance ">';
	  $xml .= "\n    <type>Report</type>\n";
	  $xml .= "    <token>$rToken</token>\n";
	  $xml .= "    <domain>$rDomain</domain>\n";
	  $xml .= "    <date>$date</date>\n";
	  $xml .= "    <page>$rPage</page>\n";
	  $xml .= "    <reportType>$rType</reportType>\n";
	  $xml .= "    <reportName>$rName</reportName>\n";
	  $xml .= "</rest>";

	  return $this->service->post($xml, $uri, NULL, 'application/x-www-form-urlencoded');
	} /* end function getReport */

	/**
	 * returns all Google Sites in the domain
	 */
	function sitesList() {
	  $uri = 'https://sites.google.com/feeds/site/';
	  $uri .= $this->domain;
	  // Apparently this is needed to get all the sites...thanks for not putting it in the docs Google!!
	  $uri .= '/?include-all-sites=true&with-mappings=true';

	  $result = $this->service->get($uri);
	  $body = $result->getBody();

	  $xml = simplexml_load_string($body);
	  if (!$xml) {
	  	throw new Exception ('Cannot load XML returned from Google');
	  }

	  $sites = array();
	  foreach ($xml->entry as $e) {
	  	$sitesns = $e->children("http://schemas.google.com/sites/2008");
	  	$url = trim((string) $e->id);
	  	$updated = trim((string) $e->updated);
	  	$last_updated = date('Y-m-d H:i:s T', strtotime($updated));
		$title = trim((string) $sitesns->siteName);
		$theme = trim((string) $sitesns->theme);
		$sites[$title] = "$title|$last_updated|$theme|$url";
	  }
	  
	  ksort($sites);
	  return $sites;
	}
	
	/**
	 * returns ACLs for a given Google Site
	 */
	function siteACL($siteName) {
	  $uri = 'https://sites.google.com/feeds/acl/site/';
	  $uri .= $this->domain;
	  // Apparently this is needed to get all the sites...thanks for not putting it in the docs Google!!
	  $uri .= "/$siteName";

	  $result = $this->service->get($uri);
	  $body = $result->getBody();
	  $body = preg_replace('/>/', ">\n", $body);
	  
	  $xml = simplexml_load_string($body);
	  if (!$xml) {
	  	throw new Exception ('Cannot load XML returned from Google');
	  }

	  $acls = array();
	  foreach ($xml->entry as $e) {
	  	$gACLns = $e->children('http://schemas.google.com/acl/2007');
		$updated = trim((string) $e->updated);
	  	$last_updated = date('Y-m-d H:i:s T', strtotime($updated));

	  	$scopeAttr = $gACLns->scope->attributes();
	  	$roleAttr = $gACLns->role->attributes();

		$scope = (string) $scopeAttr['type'];
		$value = (string) $scopeAttr['value'];
		$role = (string) $roleAttr['value'];

		$acls[] = "$siteName|$last_updated|$scope|$value|$role";
		
		}
	  
	  sort($acls);
	  return $acls;
	}

}

?>
