CREATE TABLE tx_jobrouterprocess_domain_model_process(
	name varchar(50) DEFAULT '' NOT NULL,
	connection int(11) unsigned DEFAULT '0' NOT NULL,
	processtablefields int(11) unsigned DEFAULT '0' NOT NULL,
	description text
);

CREATE TABLE tx_jobrouterprocess_domain_model_processtablefield (
	process_uid int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(20) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	field_size smallint(5) unsigned DEFAULT '0' NOT NULL,
);

CREATE TABLE tx_jobrouterprocess_domain_model_step (
	handle varchar(30) DEFAULT ''  NOT NULL,
	name varchar(255) DEFAULT ''  NOT NULL,
	process int(11) unsigned DEFAULT '0' NOT NULL,
	step_number smallint(5) unsigned DEFAULT '0' NOT NULL,
	description text,

	UNIQUE KEY handle (handle)
);

CREATE TABLE tx_jobrouterprocess_domain_model_transfer(
	uid int(11) unsigned NOT NULL AUTO_INCREMENT,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	step_uid int(11) unsigned DEFAULT '0' NOT NULL,
	correlation_id varchar(255) DEFAULT '' NOT NULL,
	type varchar(50) DEFAULT '' NOT NULL,
	initiator varchar(50) DEFAULT '' NOT NULL,
	username varchar(50) DEFAULT '' NOT NULL,
	jobfunction varchar(50) DEFAULT '' NOT NULL,
	summary text,
	priority tinyint(1) unsigned DEFAULT '0' NOT NULL,
	pool smallint(5) unsigned DEFAULT '0' NOT NULL,
	processtable text,
	encrypted_fields tinyint(1) unsigned DEFAULT '0' NOT NULL,
	start_success tinyint(1) unsigned DEFAULT '0' NOT NULL,
	start_date int(11) unsigned DEFAULT '0' NOT NULL,
	start_message text,

	PRIMARY KEY (uid),
	KEY success (start_success),
	KEY success_date (start_success, start_date)
);
