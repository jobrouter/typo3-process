CREATE TABLE tx_jobrouterprocess_domain_model_process (
	name varchar(50) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	connection int(11) unsigned DEFAULT '0' NOT NULL,
	processtablefields int(11) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_jobrouterprocess_domain_model_processtablefield (
	process_uid int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(20) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	decimal_places smallint(5) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_jobrouterprocess_domain_model_instance (
	identifier varchar(30) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	process int(11) unsigned DEFAULT '0' NOT NULL,
	step smallint(5) unsigned DEFAULT '0' NOT NULL,
	initiator varchar(50) DEFAULT '' NOT NULL,
	username varchar(50) DEFAULT '' NOT NULL,
	jobfunction varchar(50) DEFAULT '' NOT NULL,
	summary varchar(255) DEFAULT '' NOT NULL,
	priority tinyint(1) unsigned DEFAULT '0' NOT NULL,
	pool varchar(5) DEFAULT '' NOT NULL,

	UNIQUE KEY identifier (identifier)
);
