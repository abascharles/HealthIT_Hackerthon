

DROP TABLE IF EXISTS `cragpatients`;
CREATE TABLE IF NOT EXISTS `cragpatients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_name` varchar(50) DEFAULT NULL,
  `patient_number` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `sex` tinyint(4) unsigned DEFAULT NULL,

  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_number` (`patient_number`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `cragsamples`;
CREATE TABLE IF NOT EXISTS `cragsamples` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `worksheet_id` int(10) unsigned NULL  DEFAULT NULL,
  `facility_id` int(10) unsigned NOT NULL,
  `lab_id` int(10) unsigned NOT NULL DEFAULT '5',
  `parentid` int(10) unsigned DEFAULT '0',
  

  `amrs_location` tinyint(4) unsigned DEFAULT NULL,
  `provider_identifier` varchar(100) DEFAULT NULL,
  # foreign key samplestatus
  `status_id` tinyint(4) unsigned DEFAULT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `run` tinyint(3) unsigned DEFAULT '1',
  # repeatt is action of cd4db
  `repeatt` tinyint(3) unsigned DEFAULT '0',

  `receivedstatus` tinyint(3) unsigned DEFAULT NULL,
  `rejectedreason` tinyint(3) unsigned DEFAULT NULL,
  `age` tinyint(3) unsigned DEFAULT '0',

  `labcomment` varchar(100) DEFAULT NULL,

  `result` varchar(100) DEFAULT NULL,


  `approvedby` int(10) unsigned DEFAULT NULL,
  `approvedby2` int(10) unsigned DEFAULT NULL,
  # On other side, user_id is registeredby
  # user_id of 0 will be for SYSTEM GENERATED
  # dateregistered is created_at here
  `user_id` int(10) unsigned DEFAULT NULL,
  `printedby` int(10) unsigned DEFAULT NULL,
  `sent_email` tinyint(3) unsigned DEFAULT '0',

  `datecollected` date DEFAULT NULL,
  `datereceived` date DEFAULT NULL,
  `datetested` date DEFAULT NULL,
  `datemodified` date DEFAULT NULL,
  `dateapproved` date DEFAULT NULL,
  `dateapproved2` date DEFAULT NULL,
  `datedispatched` date DEFAULT NULL,
  `dateresultprinted` date DEFAULT NULL,

  `tat1` tinyint(3) unsigned DEFAULT '0',
  `tat2` tinyint(3) unsigned DEFAULT '0',
  `tat3` tinyint(3) unsigned DEFAULT '0',
  `tat4` tinyint(3) unsigned DEFAULT '0',

  `flag` tinyint(3) unsigned DEFAULT '1',

  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY(`status_id`),
  KEY `order_no` (`order_no`),
  KEY `worksheet_id` (`worksheet_id`),
  KEY `patient_id` (`patient_id`),
  KEY `facility_id` (`facility_id`),
  KEY `parentid` (`parentid`)
) ENGINE=InnoDB;



CREATE OR REPLACE VIEW crag_samples_view AS
(
  SELECT s.*, f.facilitycode, p.sex, p.dob, p.patient_number, p.patient_name 

  FROM cragsamples s
  JOIN cragpatients p ON p.id=s.patient_id
  LEFT JOIN facilitys f ON f.id=s.facility_id

);
