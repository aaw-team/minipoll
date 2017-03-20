CREATE TABLE tx_minipoll_poll (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(1) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    starttime int(11) unsigned DEFAULT '0' NOT NULL,
    endtime int(11) unsigned DEFAULT '0' NOT NULL,
    fe_group varchar(100) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    description text,
    use_captcha tinyint(1) unsigned DEFAULT '0' NOT NULL,
    duplication_check varchar(100) DEFAULT '' NOT NULL,
    status tinyint(4) unsigned DEFAULT '0' NOT NULL,
    open_datetime int(11) unsigned DEFAULT '0' NOT NULL,
    close_datetime int(11) unsigned DEFAULT '0' NOT NULL,
    allow_multiple tinyint(1) unsigned DEFAULT '0' NOT NULL,
    display_results tinyint(4) unsigned DEFAULT '0' NOT NULL,
    options int(11) unsigned DEFAULT '0' NOT NULL,
    participations int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_minipoll_poll_option (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(1) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(1) unsigned DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    poll int(11) unsigned DEFAULT '0' NOT NULL,
    answers int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY poll (poll)
);

CREATE TABLE tx_minipoll_participation (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,

    poll int(11) unsigned DEFAULT '0' NOT NULL,

    ip varchar(15) DEFAULT '' NOT NULL,
    frontend_user int(11) unsigned DEFAULT '0' NOT NULL,
    answers int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY poll (poll)
);

CREATE TABLE tx_minipoll_answer (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    participation int(11) unsigned DEFAULT '0' NOT NULL,
    poll_option int(11) unsigned DEFAULT '0' NOT NULL,
    value mediumtext,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY participation (participation)
);
