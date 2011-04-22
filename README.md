gdata-cli
=========

This command line tool makes it easy for systems administrators to
perform and script tasks for Google Apps domains. We created this library
using Zend_Gdata, in order to migrate our 2000 users over to Google
Calendar. We hope you find it useful.

Installation
------------

	git clone https://github.com/phishy/gdata-cli.git

	cp config.php.example config.php

	Modify you configuration file as needed. This tool can chat with many domains simultaneously; so
	you can configure more than one.

	./gdata-cli

	NAME
		gdata-cli - manipulate Google Apps data from the command line

	SYNOPSIS
		gdata-cli [option] METHOD [PARAMS]
		
	OPTIONS

		The available options are as follows:
		
		-domainsGoogle Apps domain name
		
	METHODS
		group.addMember
			member_id group_id 
		group.create
			id name description 
		group.delete
			group_id 
		group.info
			group_id 
		group.list
			
		group.members
			group_id 
		group.removeMember
			member_id group_id 
		nickname.list
			
		report.accounts
			reportdate 
		report.activity
			reportdate 
		report.disk_space
			reportdate 
		report.email_clients
			reportdate 
		report.quota_limit_accounts
			reportdate 
		report.summary
			reportdate 
		report.suspended_accounts
			reportdate 
		resource.list
			
		resource.listfull
			
		site.acls
			sitename 
		sites.list
			
		user.aliases
			email 
		user.create
			user first last pass 
		user.createAlias
			email alias 
		user.createNickname
			user nickname 
		user.delete
			user 
		user.deleteAlias
			email alias 
		user.deleteNickname
			nickname 
		user.expire
			user 
		user.imap
			user flag 
		user.info
			user 
		user.list
			
		user.newEmail
			old_email new_email 
		user.nicknames
			user 
		user.password
			user password 
		user.pop
			user flag 
		user.restore
			user 
		user.suspend
			user 
		user.unexpire
			user 
		user.webclip
			user flag 

		
	AUTHORS
		Jeff Loiselle <jeff@broadinstitute.org>
		Andrew Teixeira <teixeira@broadinstitute.org>




