-- Dumping structure for table nhrl_db.age_categories
DROP TABLE IF EXISTS `age_categories`;
CREATE TABLE IF NOT EXISTS `age_categories` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lower` tinyint(3) unsigned DEFAULT NULL,
  `upper` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



INSERT INTO `age_categories` (`name`, `lower`, `upper`) VALUES
  ('No Data', null, null),
  ('Less 2y', 0, 2),
  ('2-9', 3, 9),
  ('10-14', 10, 14),
  ('15-19', 15, 19),
  ('20-24', 20, 24),
  ('25+', 25, 110);



-- Dumping structure for table nhrl_db.dr_calls
DROP TABLE IF EXISTS `dr_calls`;
CREATE TABLE IF NOT EXISTS `dr_calls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sample_id` int(10) unsigned NOT NULL,
  `drug_class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `drug_class_id` tinyint(3) unsigned DEFAULT NULL,
  `mutations` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_calls_sample_id_index` (`sample_id`),
  KEY `dr_calls_drug_class_id_index` (`drug_class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table nhrl_db.dr_call_drugs
DROP TABLE IF EXISTS `dr_call_drugs`;
CREATE TABLE IF NOT EXISTS `dr_call_drugs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `call_id` int(10) unsigned NOT NULL,
  `short_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_name_id` tinyint(3) unsigned DEFAULT NULL,
  `call` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `score` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_call_drugs_call_id_index` (`call_id`),
  KEY `dr_call_drugs_short_name_id_index` (`short_name_id`),
  KEY `dr_call_drugs_call_index` (`call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table nhrl_db.dr_genotypes
DROP TABLE IF EXISTS `dr_genotypes`;
CREATE TABLE IF NOT EXISTS `dr_genotypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sample_id` int(10) unsigned NOT NULL,
  `locus` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locus_id` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_genotypes_sample_id_index` (`sample_id`),
  KEY `dr_genotypes_locus_id_index` (`locus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table nhrl_db.dr_residues
DROP TABLE IF EXISTS `dr_residues`;
CREATE TABLE IF NOT EXISTS `dr_residues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `genotype_id` int(10) unsigned NOT NULL,
  `residue` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `residue_id` smallint(5) unsigned DEFAULT NULL,
  `position` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_residues_genotype_id_index` (`genotype_id`),
  KEY `dr_residues_residue_id_index` (`residue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table nhrl_db.dr_samples
DROP TABLE IF EXISTS `dr_samples`;
CREATE TABLE IF NOT EXISTS `dr_samples` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `facility_id` int(10) unsigned NOT NULL,
  `lab_id` tinyint(3) unsigned NOT NULL,
  `clinician_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `control` tinyint(3) unsigned DEFAULT '0',
  `exatype_id` bigint(20) unsigned DEFAULT NULL,
  `prev_prophylaxis` tinyint(3) unsigned DEFAULT NULL,
  `prophylaxis` tinyint(3) unsigned DEFAULT NULL,
  `receivedstatus` tinyint(3) unsigned DEFAULT NULL,
  `rejectedreason` tinyint(3) unsigned DEFAULT NULL,
  `project` tinyint(3) unsigned DEFAULT NULL,
  `sampletype` tinyint(3) unsigned DEFAULT NULL,
  `container_type` tinyint(3) unsigned DEFAULT NULL,
  `amount_unit` tinyint(3) unsigned DEFAULT NULL,
  `sample_amount` smallint(5) unsigned DEFAULT NULL,
  `age` tinyint(3) unsigned DEFAULT NULL,
  `clinical_indications` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_opportunistic_infections` tinyint(1) NOT NULL DEFAULT '0',
  `opportunistic_infections` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_tb` tinyint(1) NOT NULL DEFAULT '0',
  `tb_treatment_phase_id` tinyint(3) unsigned DEFAULT NULL,
  `has_arv_toxicity` tinyint(1) NOT NULL DEFAULT '0',
  `arv_toxicities` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cd4_result` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_missed_pills` tinyint(1) NOT NULL DEFAULT '0',
  `missed_pills` smallint(5) unsigned DEFAULT NULL,
  `has_missed_visits` tinyint(1) NOT NULL DEFAULT '0',
  `missed_visits` smallint(5) unsigned DEFAULT NULL,
  `has_missed_pills_because_missed_visits` tinyint(1) NOT NULL DEFAULT '0',
  `other_medications` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vl_result1` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vl_result2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vl_result3` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vl_date_result1` date DEFAULT NULL,
  `vl_date_result2` date DEFAULT NULL,
  `vl_date_result3` date DEFAULT NULL,
  `repeatt` tinyint(1) NOT NULL DEFAULT '0',
  `run` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `parentid` int(10) unsigned DEFAULT '0',
  `collect_new_sample` tinyint(1) NOT NULL DEFAULT '0',
  `date_prev_regimen` date DEFAULT NULL,
  `date_current_regimen` date DEFAULT NULL,
  `bulk_registration_id` int(10) unsigned DEFAULT NULL,
  `extraction_worksheet_id` int(10) unsigned DEFAULT NULL,
  `worksheet_id` int(10) unsigned DEFAULT NULL,
  `datecollected` date DEFAULT NULL,
  `datereceived` date DEFAULT NULL,
  `datetested` date DEFAULT NULL,
  `datedispatched` date DEFAULT NULL,
  `approvedby` int(10) unsigned DEFAULT NULL,
  `approvedby2` int(10) unsigned DEFAULT NULL,
  `dateapproved` date DEFAULT NULL,
  `dateapproved2` date DEFAULT NULL,
  `dr_reason_id` tinyint(3) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `received_by` int(10) unsigned DEFAULT NULL,
  `passed_gel_documentation` tinyint(1) DEFAULT NULL,
  `status_id` tinyint(3) unsigned DEFAULT NULL,
  `qc_pass` tinyint(1) DEFAULT '0',
  `qc_stop_codon_pass` tinyint(1) DEFAULT '0',
  `qc_plate_contamination_pass` tinyint(1) DEFAULT '0',
  `qc_frameshift_codon_pass` tinyint(1) DEFAULT '0',
  `qc_distance_to_sample` smallint(5) unsigned DEFAULT NULL,
  `qc_distance_from_sample` smallint(5) unsigned DEFAULT NULL,
  `qc_distance_difference` double(4,3) unsigned DEFAULT NULL,
  `qc_distance_strain_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_distance_compare_to_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qc_distance_sample_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_errors` tinyint(1) NOT NULL DEFAULT '0',
  `has_warnings` tinyint(1) NOT NULL DEFAULT '0',
  `has_mutations` tinyint(1) NOT NULL DEFAULT '0',
  `pending_manual_intervention` tinyint(1) NOT NULL DEFAULT '0',
  `had_manual_intervention` tinyint(1) NOT NULL DEFAULT '0',
  `assembled_sequence` text COLLATE utf8_unicode_ci,
  `chromatogram_url` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exatype_version` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `algorithm` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `synched` tinyint(4) DEFAULT '0',
  `datesynched` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_samples_patient_id_index` (`patient_id`),
  KEY `dr_samples_facility_id_index` (`facility_id`),
  KEY `dr_samples_lab_id_index` (`lab_id`),
  KEY `dr_samples_exatype_id_index` (`exatype_id`),
  KEY `dr_samples_parentid_index` (`parentid`),
  KEY `dr_samples_extraction_worksheet_id_index` (`extraction_worksheet_id`),
  KEY `dr_samples_worksheet_id_index` (`worksheet_id`),
  KEY `dr_samples_dr_reason_id_index` (`dr_reason_id`),
  KEY `dr_samples_user_id_index` (`user_id`),
  KEY `dr_samples_status_id_index` (`status_id`),
  KEY `bulk_registration_id` (`bulk_registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table nhrl_db.dr_warnings
DROP TABLE IF EXISTS `dr_warnings`;
CREATE TABLE IF NOT EXISTS `dr_warnings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sample_id` int(10) unsigned NOT NULL,
  `warning_id` int(10) unsigned NOT NULL,
  `system` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_warnings_sample_id_index` (`sample_id`),
  KEY `dr_warnings_warning_id_index` (`warning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table nhrl_db.dr_worksheets
DROP TABLE IF EXISTS `dr_worksheets`;
CREATE TABLE IF NOT EXISTS `dr_worksheets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lab_id` tinyint(3) unsigned NOT NULL,
  `plate_id` bigint(20) unsigned DEFAULT NULL,
  `extraction_worksheet_id` int(10) unsigned DEFAULT NULL,
  `status_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exatype_status_id` tinyint(3) unsigned NOT NULL DEFAULT '4',
  `dateuploaded` date DEFAULT NULL,
  `datecancelled` date DEFAULT NULL,
  `datereviewed` date DEFAULT NULL,
  `datereviewed2` date DEFAULT NULL,
  `createdby` int(10) unsigned DEFAULT NULL,
  `runby` int(10) unsigned DEFAULT NULL,
  `uploadedby` int(10) unsigned DEFAULT NULL,
  `cancelledby` int(10) unsigned DEFAULT NULL,
  `reviewedby` int(10) unsigned DEFAULT NULL,
  `reviewedby2` int(10) unsigned DEFAULT NULL,
  `time_sent_to_exatype` datetime DEFAULT NULL,
  `qc_run` tinyint(1) NOT NULL DEFAULT '0',
  `qc_pass` tinyint(1) NOT NULL DEFAULT '0',
  `qc_distance_pass` int(11) DEFAULT NULL,
  `plate_controls_pass` tinyint(1) NOT NULL DEFAULT '0',
  `has_errors` tinyint(1) NOT NULL DEFAULT '0',
  `has_warnings` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_worksheets_plate_id_index` (`plate_id`),
  KEY `dr_worksheets_extraction_worksheet_id_index` (`extraction_worksheet_id`),
  KEY `dr_worksheets_status_id_index` (`status_id`),
  KEY `dr_worksheets_sanger_status_id_index` (`exatype_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table nhrl_db.dr_worksheet_warnings
DROP TABLE IF EXISTS `dr_worksheet_warnings`;
CREATE TABLE IF NOT EXISTS `dr_worksheet_warnings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `worksheet_id` int(10) unsigned NOT NULL,
  `warning_id` int(10) unsigned NOT NULL,
  `system` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dr_worksheet_warnings_worksheet_id_index` (`worksheet_id`),
  KEY `dr_worksheet_warnings_warning_id_index` (`warning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for view nhrl_db.dr_calls_view
DROP VIEW IF EXISTS `dr_calls_view`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `dr_calls_view`;
CREATE OR REPLACE VIEW `dr_calls_view` AS (select `cd`.`id` AS `id`,`cd`.`call_id` AS `call_id`,`cd`.`short_name` AS `short_name`,`cd`.`short_name_id` AS `short_name_id`,`cd`.`call` AS `call`,`cd`.`created_at` AS `created_at`,`cd`.`updated_at` AS `updated_at`,`c`.`sample_id` AS `sample_id`,`c`.`drug_class` AS `drug_class`,`c`.`drug_class_id` AS `drug_class_id`,`c`.`mutations` AS `mutations`,`s`.`patient_id` AS `patient_id`,`s`.`facility_id` AS `facility_id` from ((`dr_call_drugs` `cd` left join `dr_calls` `c` on((`c`.`id` = `cd`.`call_id`))) left join `dr_samples` `s` on((`c`.`sample_id` = `s`.`id`))));

-- Dumping structure for view nhrl_db.dr_genotypes_views
DROP VIEW IF EXISTS `dr_genotypes_views`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `dr_genotypes_views`;
CREATE OR REPLACE VIEW `dr_genotypes_views` AS (select `r`.`id` AS `id`,`r`.`genotype_id` AS `genotype_id`,`r`.`residue` AS `residue`,`r`.`residue_id` AS `residue_id`,`r`.`position` AS `position`,`r`.`created_at` AS `created_at`,`r`.`updated_at` AS `updated_at`,`g`.`sample_id` AS `sample_id`,`g`.`locus` AS `locus`,`g`.`locus_id` AS `locus_id`,`s`.`patient_id` AS `patient_id`,`s`.`facility_id` AS `facility_id` from ((`dr_genotypes` `g` left join `dr_residues` `r` on((`g`.`id` = `r`.`genotype_id`))) left join `dr_samples` `s` on((`g`.`sample_id` = `s`.`id`))));


CREATE OR REPLACE VIEW dr_samples_view AS
(
SELECT s.*, p.facility_id, f.facilitycode, f.name as facilityname,
p.national_patient_id, p.patient, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

FROM dr_samples s
LEFT JOIN viralpatients p ON p.id=s.patient_id
LEFT JOIN facilitys f ON f.id=p.facility_id

);
