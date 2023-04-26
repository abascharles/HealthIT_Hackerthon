DROP TABLE IF EXISTS `dr_plate_statuses`;
CREATE TABLE  `dr_plate_statuses` (
  `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
  `other_id` INT unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `output` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


INSERT INTO `dr_plate_statuses` (`id`, `other_id`, `name`, `output`) VALUES
	(1, 2100, 'completed','<strong><div style=\'color: #339900;\'>Completed</div></strong>'),
	(2, 2101, 'error','<strong><div style=\'color: #FF0000;\'>Error</div></strong>'),
	(3, 2102, 'failed','<strong><div style=\'color: #FF0000;\'>Failed</div></strong>'),
	(4, 2110, 'pending','<strong><div style=\'color: #0000FF;\'>Pending</div></strong>'),
	(5, 2120, 'action_required','<strong><div style=\'color: #FFD324;\'>Action Required</div></strong>'),
  (6, 2119, 'finalising','<strong><div style=\'color: #0000FF;\'>Finalising</div></strong>');

DROP TABLE IF EXISTS `dr_job_statuses`;
CREATE TABLE  `dr_job_statuses` (
  `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
  `other_id` INT unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `output` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


INSERT INTO `dr_job_statuses` (`id`, `other_id`, `name`, `output`) VALUES
  (1, 4100, 'completed','<strong><div style=\'color: #339900;\'>Completed</div></strong>'),
  (2, 4101, 'error','<strong><div style=\'color: #FF0000;\'>Error</div></strong>'),
  (3, 4102, 'failed','<strong><div style=\'color: #FF0000;\'>Failed</div></strong>'),
  (4, 4110, 'pending','<strong><div style=\'color: #0000FF;\'>Pending</div></strong>'),
  (5, 4120, 'action_required','<strong><div style=\'color: #FFD324;\'>Action Required</div></strong>'),
  (6, 4119, 'finalising','<strong><div style=\'color: #0000FF;\'>In Process</div></strong>');

DROP TABLE IF EXISTS `dr_contig_statuses`;
CREATE TABLE  `dr_contig_statuses` (
  `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
  `other_id` INT unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `output` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


INSERT INTO `dr_contig_statuses` (`id`, `other_id`, `name`, `output`) VALUES
  (1, 6100, 'completed','<strong><div style=\'color: #339900;\'>Completed</div></strong>'),
  (2, 6101, 'error','<strong><div style=\'color: #FF0000;\'>Error</div></strong>'),
  (3, 6102, 'failed','<strong><div style=\'color: #FF0000;\'>Failed</div></strong>'),
  (4, 6110, 'pending','<strong><div style=\'color: #0000FF;\'>Pending</div></strong>'),
  (5, 6120, 'action_required','<strong><div style=\'color: #FFD324;\'>Action Required</div></strong>'),
  (6, 6119, 'finalising','<strong><div style=\'color: #0000FF;\'>In Process</div></strong>'),
  (7, 6111, 'processing','<strong><div style=\'color: #0000FF;\'>Processing</div></strong>');


DROP TABLE IF EXISTS `dr_sample_statuses`;
CREATE TABLE  `dr_sample_statuses` (
  `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
  `other_id` INT unsigned NOT NULL,
  `name` varchar(70) DEFAULT NULL,
  `output` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `dr_sample_statuses` (`id`, `other_id`, `name`, `output`) VALUES
	(1, 1100, 'completed','<strong><div style=\'color: #339900;\'>Completed</div></strong>'),
	(2, 1101, 'error','<strong><div style=\'color: #FF0000;\'>Error</div></strong>'),
	(3, 1102, 'failed','<strong><div style=\'color: #FF0000;\'>Failed</div></strong>'),
	(4, 1110, 'pending','<strong><div style=\'color: #0000FF;\'>Pending</div></strong>'),
	(5, 1111, 'processing','<strong><div style=\'color: #0000FF;\'>Processing</div></strong>'),
	(6, 1120, 'action_required','<strong><div style=\'color: #FFD324;\'>Action Required</div></strong>'),
	(7, 1112, 'qc_pending','<strong><div style=\'color: #FFD324;\'>Quality Control Pending</div></strong>'),
  (8, 1610, 'pending_contig','<strong><div style=\'color: #FFD324;\'>At least one contig is still pending</div></strong>'),
  (9, 1113, 'constructing','<strong><div style=\'color: #FFD324;\'>The job is still being constructed</div></strong>'),
  (10, 1119, 'discordant','<strong><div style=\'color: #FFD324;\'>Two or more contigs in this sample have different statuses</div></strong>');

-- DROP TABLE IF EXISTS `calls`;
-- CREATE TABLE  `calls` (
--   `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
--   `name` varchar(50) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- INSERT INTO `calls` (`id`, `name`) VALUES
-- 	(1, 'S'),
-- 	(2, 'LC');


DROP TABLE IF EXISTS `dr_warning_codes`;
CREATE TABLE  `dr_warning_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `error` TINYINT unsigned NOT NULL DEFAULT 0,
  `code` int unsigned DEFAULT 0,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(80) DEFAULT NULL,
  `type` varchar(150) DEFAULT NULL,
  `message` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


INSERT INTO `dr_warning_codes` (`id`, `error`, `name`, `description`) VALUES
	(1, 1, 'ErrRecall', 'Error in the recall step'),
	(2, 1, 'ErrRecallSampleFailed', 'Sample failed by recall'),
	(3, 1, 'ErrRecallUserFailed', 'User manually marked the sample as failed'),
	(4, 1, 'ErrAlign', 'Error in the alignment step'),
	(5, 1, 'ErrDrugCall', 'Error in the drug class step'),
	(6, 1, 'ErrNegativeControl', 'Negative control failed'),
	(7, 1, 'ErrPositiveControl', 'Positive control failed'),
	(8, 1, 'ErrUnknownSampleType', 'Unknown sample type'),
	(9, 1, 'ErrPlateControlFailed', 'At least one of the plate’s control samples failed'),
	(10, 1, 'ErrPlateAllSamplesFailed', 'All the samples for a plate failed'),
	(11, 1, 'ErrOther', 'Other error'),
	(12, 0, 'WrnRecallBasecall', 'Warning from recall'),
	(13, 0, 'WrnRecallUserApproved', 'The user manually approved a sample flagged by recall'),
	(14, 0, 'WrnNoPositiveControl', 'The plate had no positive control'),
	(15, 0, 'WrnNoNegativeControl', 'The plate had no negative control'),
	(16, 0, 'WrnRecallManualReviewNeeded', 'Manual review is required by recall'),
	(17, 1, 'ErrInsufficientLicenses', 'The licenses are insufficient.'),
  (23, 1, 'ErrBasecallSampleFailed', NULL),
  (30, 0, 'WrnBasecallBasecall', NULL),
  (37, 0, 'WrnBasecallManualReviewNeeded', NULL),
  (44, 0, 'WrnBasecallUserApproved', NULL),
  (47, 1, 'ErrSampleInvalidFragments', NULL),
  (48, 1, 'ErrBasecallUserFailed', NULL),
  (49, 0, 'WrnBasecallManualReviewEnforce', NULL);




-- DROP TABLE IF EXISTS `dr_viralprophylaxis`;
-- CREATE TABLE IF NOT EXISTS `dr_viralprophylaxis` (
--   `id` TINYINT unsigned NOT NULL AUTO_INCREMENT,
--   `displaylabel` varchar(50) NOT NULL,
--   `name` varchar(30) NOT NULL,
--   `regimen1` varchar(10) NULL,
--   `regimen1_class_id` TINYINT unsigned NULL,
--   `regimen2` varchar(10) NULL,
--   `regimen2_class_id` TINYINT unsigned NULL,
--   `regimen3` varchar(10) NULL,
--   `regimen3_class_id` TINYINT unsigned NULL,
--   `description` varchar(100) NULL,
--   `line` TINYINT unsigned NOT NULL,
--   `ptype` TINYINT unsigned NOT NULL DEFAULT '2',
--   `category` TINYINT unsigned NOT NULL DEFAULT '3',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;


-- INSERT INTO `dr_viralprophylaxis` (`id`, `displaylabel`, `name`, `regimen1`, `regimen1_class_id`, `regimen2`, `regimen2_class_id`, `regimen3`, `regimen3_class_id`, `line`, `ptype`, `category`) VALUES
-- 	(1, '4 &nbsp;AZT+3TC+NVP', 'AZT+3TC+NVP', 'AZT', 12, '3TC', 10, 'NVP', 8, 0, 0, 4),
-- 	(2, '5 &nbsp;AZT+3TC+EFV', 'AZT+3TC+EFV', 'AZT', 12, '3TC', 10, 'EFV', 6, 0, 0, 5),
-- 	(3, '2 &nbsp;TDF+3TC+NVP', 'TDF+3TC+NVP', 'TDF', 16, '3TC', 10, 'NVP', 8, 0, 0, 2),
-- 	(4, '1 &nbsp;TDF+3TC+EFV', 'TDF+3TC+EFV', 'TDF', 16, '3TC', 10, 'EFV', 6, 0, 0, 1),
-- 	(5, '10 &nbsp;AZT+3TC+LPVr', 'AZT+3TC+LPVr', 'AZT', 12, '3TC', 10, 'LPVr', 21, 0, 0, 10),
-- 	(6, '17 &nbsp;AZT+3TC+ABC', 'AZT+3TC+ABC', 'AZT', 12, '3TC', 10, 'ABC', 11, 0, 0, 17),
-- 	(7, '18 &nbsp;TDF+3TC+LPVr', 'TDF+3TC+LPVr', 'TDF', 16, '3TC', 10, 'LPVr', 21, 0, 0, 18),
-- 	(8, '14 &nbsp;AZT+3TC+ATVr', 'AZT+3TC+ATVr', 'AZT', 12, '3TC', 10, 'ATVr', 17, 0, 0, 14),
-- 	(9, '11 &nbsp;TDF+3TC+ATVr', 'TDF+3TC+ATVr', 'TDF', 16, '3TC', 10, 'ATVr', 17, 0, 0, 11),
-- 	(10, '13 &nbsp;ABC+3TC+ATVr', 'ABC+3TC+ATVr', 'ABC', 11, '3TC', 10, 'ATVr', 17, 0, 0, 13),
-- 	(11, '6 &nbsp;ABC+3TC+NVP', 'ABC+3TC+NVP', 'ABC', 11, '3TC', 10, 'NVP', 8, 0, 0, 6),
-- 	(12, '7 &nbsp;ABC+3TC+EFV', 'ABC+3TC+EFV', 'ABC', 11, '3TC', 10, 'EFV', 6, 0, 0, 7),
-- 	(13, '9 &nbsp;ABC+3TC+LPVr', 'ABC+3TC+LPVr', 'ABC', 11, '3TC', 10, 'LPVr', 21, 0, 0, 9),
-- 	(14, '16 &nbsp;Other', 'Other', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 16),
-- 	(15, '19 &nbsp;None', 'None', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 19),
-- 	(16, '20 &nbsp;No Data', 'No Data', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 20),
-- 	(17, '3 &nbsp;TDF+3TC+DTG', 'TLD', 'TDF', 16, '3TC', 10, 'DTG', 2, 0, 0, 3),
-- 	(18, '8 &nbsp;ABC+3TC+DTG', 'ABC+3TC+DTG', 'ABC', 11, '3TC', 10, 'DTG', 2, 0, 0, 8),
-- 	(19, '15 &nbsp;AZT+3TC+DRV/r', 'AZT+3TC+DRV/r', 'AZT', 12, '3TC', 10, 'DRV/r', 18, 0, 0, 15);



DROP TABLE IF EXISTS `regimen_classes`;
CREATE TABLE `regimen_classes` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drug_class` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drug_class_id` tinyint(3) unsigned DEFAULT NULL,
  `short_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `regimen_classes` VALUES 
	(1,NULL,'INSTI',1,'BIC'),
	(2,NULL,'INSTI',1,'DTG'),
	(3,NULL,'INSTI',1,'EVG'),
	(4,NULL,'INSTI',1,'RAL'),
	(5,NULL,'NNRTI',2,'DOR'),
	(6,NULL,'NNRTI',2,'EFV'),
	(7,NULL,'NNRTI',2,'ETR'),
	(8,NULL,'NNRTI',2,'NVP'),
	(9,NULL,'NNRTI',2,'RPV'),
	(10,NULL,'NRTI',3,'3TC'),
	(11,NULL,'NRTI',3,'ABC'),
	(12,NULL,'NRTI',3,'AZT'),
	(13,NULL,'NRTI',3,'D4T'),
	(14,NULL,'NRTI',3,'DDI'),
	(15,NULL,'NRTI',3,'FTC'),
	(16,NULL,'NRTI',3,'TDF'),
	(17,NULL,'PI',4,'ATV/r'),
	(18,NULL,'PI',4,'DRV/r'),
	(19,NULL,'PI',4,'FPV/r'),
	(20,NULL,'PI',4,'IDV/r'),
	(21,NULL,'PI',4,'LPV/r'),
	(22,NULL,'PI',4,'NFV'),
	(23,NULL,'PI',4,'SQV/r'),
	(24,NULL,'PI',4,'TPV/r');



--
-- Table structure for table `worksheetstatus`
--

DROP TABLE IF EXISTS `worksheetstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worksheetstatus` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` varchar(100) DEFAULT NULL,
  `output` varchar(150) DEFAULT NULL,
  `active` TINYINT UNSIGNED DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worksheetstatus`
--

LOCK TABLES `worksheetstatus` WRITE;
/*!40000 ALTER TABLE `worksheetstatus` DISABLE KEYS */;
INSERT INTO `worksheetstatus` VALUES 
(1,'In-Process','<strong><div style=\'color: #FFD324;\'>In-Process</div></strong>',1),
(2,'Tested','<strong><div style=\'color: #0000FF;\'>Tested</div></strong>',1),
(3,'Approved','<strong><div style=\'color: #339900;\'>Approved</div></strong>',1),
(4,'Cancelled','<strong><div style=\'color: #FF0000;\'>Cancelled</div></strong>',1),
(5,'Sent To Exatype','<strong><div style=\'color: #0000FF;\'>Sent To Exatype</div></strong>',1),
(6,'Received From Exatype','<strong><div style=\'color: #0000FF;\'>Received From Exatype</div></strong>',1),
(7,'Failed','<strong><div style=\'color: #FF0000;\'>Failed</div></strong>',1);
/*!40000 ALTER TABLE `worksheetstatus` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `drug_resistance_reasons`;

CREATE TABLE `drug_resistance_reasons` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  -- `rank` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `drug_resistance_reasons` (`id`, `name`, `description`) VALUES
(1, 'Consistent Non-Suppression',  'Two consecutive tests with viralloads above 1000.'),
(2, 'Paed Non-Suppression',  'Patient aged 19 and under with a viralload above 1000.'),
(3, 'PMTCT Non-Suppression',  'PMTCT patient viralload above 1000.'),
(4, 'Confirmed 2nd line treatment failure',  'Confirmed 2nd line treatment failure.'),
(5, 'Patient failing 1st line PI based regimen',  'Patient failing 1st line PI based regimen.'),
(6, 'Third line request',  'Third line request.'),
(7, 'Study request',  'Study request'),
(8, 'Other',  'Other'),
(9, 'PrEP Seroconversion',  'PrEP Seroconversion');

DROP TABLE IF EXISTS `dr_primers`;

CREATE TABLE `dr_primers` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `dr_primers` (`id`, `name`, `full_name`) VALUES
(1, 'F1', 'Forward Primer 1'),
(2, 'F2', 'Forward Primer 2'),
(3, 'F3', 'Forward Primer 3'),
(4, 'R1', 'Reverse Primer 1'),
(5, 'R2', 'Reverse Primer 2'),
(6, 'R3', 'Reverse Primer 3');


DROP TABLE IF EXISTS `dr_patient_statuses`;
CREATE TABLE `dr_patient_statuses` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `dr_patient_statuses` (`id`, `name`) VALUES
(1, 'Awaiting Test'),
(2, 'Sample Created'),
(3, 'Completed Test'),
(4, 'Insufficient sample'),
(5, 'Failed Test');


DROP TABLE IF EXISTS `dr_projects`;
CREATE TABLE `dr_projects` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `dr_projects` (`id`, `name`) VALUES
(1, 'Public'),
(2, 'Surveillance'),
(3, 'Study'),
(4, 'ADR'),
(5, 'Mortuary Study'),
(6, 'PDR'),
(7, 'Pediatric DR Study'),
(8, 'PSC Request'),
(9, 'Research World Limited Requests');


DROP TABLE IF EXISTS `dr_rejected_reasons`;
CREATE TABLE `dr_rejected_reasons` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `dr_rejected_reasons` (`id`, `name`) VALUES
(1, 'Insufficient sample volumes'),
(2, 'EID sample over 3 weeks old from time collected'),
(3, 'Haemolysed sample'),
(4, 'Specimen collected in tubes other than the specified (EDTA &PPT)'),
(5, 'Clotted sample'),
(6, 'Leaking samples due to broken tubes or properly unscrewed vials'),
(7, 'Unlabelled or mislabeled specimen'),
(8, 'Poor storage and transportation of the sample as per the specific SOPs'),
(9, 'Incomplete request form or form not matching the corresponding tube'),
(10, 'Specimen shipped without request forms'),
(11, 'Plasma separated after 6 hours and PPT samples received beyond 24 hour after draw.');


DROP TABLE IF EXISTS `tb_treatment_phases`;
CREATE TABLE `tb_treatment_phases` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `tb_treatment_phases` (`id`, `name`) VALUES
(1, 'None'),
(2, 'Intensive'),
(3, 'Continuation');

DROP TABLE IF EXISTS `container_types`;
CREATE TABLE `container_types` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `container_types` (`id`, `name`) VALUES
(1, 'PPT Tube'),
(2, 'EDTA Tube');


DROP TABLE IF EXISTS `amount_units`;
CREATE TABLE `amount_units` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `amount_units` (`id`, `name`) VALUES
(1, 'uL'),
(2, 'spots');


DROP TABLE IF EXISTS `clinical_indications`;
CREATE TABLE `clinical_indications` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `clinical_indications` (`id`, `name`) VALUES
(1, 'New or recurrent WHO stage 3 or 4 conditions after >= 6 months of ART'),
(2, 'New or recurrent papular pruritic eruptions (PPE) after >= 6 months of ART'),
(3, 'Poor or decline in growth despite giving ART over a period of >=6 months and after treating for and excluding other causes e.g. TB, malnutrition'),
(4, 'Failure to meet neuro-development milestones after >=6 months of art'), 
(5, 'Recurrence of infections that are severe, persistent or refractory to treatment after >=6 months of ART');



DROP TABLE IF EXISTS `arv_toxicities`;
CREATE TABLE `arv_toxicities` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `arv_toxicities` (`id`, `name`) VALUES
(1, 'Renal'),
(2, 'Lipid'),
(3, 'Liver');



DROP TABLE IF EXISTS `other_medications`;
CREATE TABLE `other_medications` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `other_medications` (`id`, `name`) VALUES
(1, 'Rifampicin based TB  drugs'),
(2, 'Ketoconazole'),
(3, 'Cotrimoxazole'),
(4, 'Fluconazole'),
(5, 'Dapsone'),
(6, 'Multivitamin'),
(7, 'Hormonal contraceptives');

