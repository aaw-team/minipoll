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
    useCaptcha tinyint(1) unsigned DEFAULT '0' NOT NULL,
    duplicationCheck varchar(100) DEFAULT '' NOT NULL,
    closeDatetime int(11) unsigned DEFAULT '0' NOT NULL,
    options int(11) unsigned DEFAULT '0' NOT NULL,
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
    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY poll (poll)
);

CREATE TABLE tx_minipoll_poll_participation (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,

    poll int(11) unsigned DEFAULT '0' NOT NULL,

    userAgent mediumtext,
    ip varchar(15) DEFAULT '' NOT NULL,
    fe_user int(11) unsigned DEFAULT '0' NOT NULL,
    answers int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY poll (poll)
);

CREATE TABLE tx_minipoll_poll_answer (
    participation_uid int(11) unsigned DEFAULT '0' NOT NULL,
    option_uid int(11) unsigned DEFAULT '0' NOT NULL,
    value mediumtext,

    PRIMARY KEY (participation_uid,option_uid)
);
