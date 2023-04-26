# s.regimen found, ask meaning
# s.receivedby not found 
# s.receivedby as received_by 
CREATE OR REPLACE VIEW old_samples_view AS
(
    SELECT s.id, s.originalid as original_sample_id,  
    s.AMRSlocation as amrs_location, s.provideridentifier as provider_identifier, s.orderno as order_no,
    s.sampletype as sample_type, s.receivedstatus, p.age,  s.pcrtype, p.prophylaxis as regimen, 
    m.prophylaxis as mother_prophylaxis, m.feeding, s.spots, s.comments, s.labcomment, s.parentid, 
    s.rejectedreason, s.reason_for_repeat, s.interpretation, s.result, s.worksheet as worksheet_id,
    s.hei_validation, s.enrollmentCCCno as enrollment_ccc_no, s.enrollmentstatus as enrollment_status, s.referredfromsite,
    s.otherreason, s.flag, s.run, s.repeatt, s.eqa, s.approvedby, s.approved2by as approvedby2, 
    s.datecollected, s.datetested, s.datemodified, s.dateapproved, s.dateapproved2,
    s.patientsmsdatesent as time_result_sms_sent,
    #s.tat1, s.tat2, s.tat3, s.tat4, s.previous_positive, 
    s.synched, s.datesynched, s.dateentered as created_at,
    m.lastvl as mother_last_result, m.age as mother_age,


    s.batchno as original_batch_id, s.highpriority, s.inputcomplete as input_complete, s.batchcomplete as 
    batch_complete, s.siteentry as site_entry, s.sentemail as sent_email, 
    s.printedby, s.userid as user_id, s.receivedby as received_by,
    s.labtestedin as lab_id, s.facility as facility_id, 
    s.datedispatchedfromfacility, s.datereceived, s.datebatchprinted, s.datedispatched, 
    s.dateindividualresultprinted,  

    p.originalautoid as original_patient_id, s.patient, s.fullnames as patient_name, s.caregiverphoneno as 
    caregiver_phone, p.gender, m.entry_point,  s.dateinitiatedontreatment, p.dob, 
    s.patientphoneno as patient_phone_no, s.patientlanguage as preferred_language,

    m.status as hiv_status, m.cccno as ccc_no

    FROM samples s
    LEFT JOIN patients p ON p.autoID=s.patientAUTOid
    LEFT JOIN mothers m ON m.id=p.mother

); 

CREATE OR REPLACE VIEW old_viralsamples_view AS
(
    SELECT s.id, s.originalid as original_sample_id, 
    s.AMRSlocation as amrs_location, s.provideridentifier as provider_identifier, s.orderno as order_no,
    s.vlrequestno as vl_test_request_no, s.receivedstatus, p.age, s.age2 as age_category, s.justification,
    s.otherjustification as other_justification, s.sampletype, s.prophylaxis, s.regimenline, p.pmtct,
    s.dilutionfactor, s.dilutiontype, s.comments, s.labcomment, s.parentid, s.rejectedreason, s.reason_for_repeat,
    s.rcategory, s.result, s.units, s.interpretation, s.worksheet as worksheet_id, s.flag, s.run, s.repeatt, s.approvedby,
    s.approved2by as approvedby2, s.datecollected, s.datetested, s.datemodified, s.dateapproved, s.dateapproved2,
    #s.patientsmsdatesent as time_result_sms_sent,
    #s.tat1, s.tat2, s.tat3, s.tat4,  s.previous_nonsuppressed,
    s.synched, s.datesynched, s.dateentered as created_at,

    s.batchno as original_batch_id, s.highpriority, s.inputcomplete as input_complete, s.batchcomplete as 
    batch_complete, s.siteentry as site_entry, s.sentemail as sent_email, 
    s.printedby, s.userid as user_id, s.receivedby as received_by,
    s.labtestedin as lab_id, s.facility as facility_id, 
    s.datedispatchedfromfacility, s.datereceived, s.datebatchprinted, s.datedispatched, 
    s.dateindividualresultprinted, 

    p.originalautoid as original_patient_id, s.patient, s.fullnames as patient_name, s.caregiverphoneno as 
    caregiver_phone, p.gender, p.initiationdate as initiation_date, p.dob, 
    s.patientphoneno as patient_phone_no, s.patientlanguage as preferred_language

    FROM viralsamples s
    LEFT JOIN viralpatients p ON p.AutoID=s.patientid

);

CREATE OR REPLACE VIEW old_viralsamples_1214_view AS
(
    SELECT s.id, 
    s.receivedstatus, p.age, s.justification,
    s.otherjustification as other_justification, s.sampletype, s.prophylaxis, s.regimenline, p.pmtct,
    s.dilutionfactor, s.dilutiontype, s.comments, s.labcomment, s.parentid, s.rejectedreason, s.reason_for_repeat,
    s.result, s.units, s.interpretation, s.worksheet as worksheet_id, s.flag, s.run, s.repeatt, s.approvedby,
    s.datecollected, s.datetested, s.datemodified, s.dateapproved,
    #s.tat1, s.tat2, s.tat3, s.tat4,  s.previous_nonsuppressed,
    s.synched, s.datesynched, s.dateentered as created_at,

    s.batchno as original_batch_id, s.inputcomplete as input_complete, s.batchcomplete as 
    batch_complete, s.siteentry as site_entry, s.sentemail as sent_email, s.printedby, s.userid as user_id, 
    s.labtestedin as lab_id, s.facility as facility_id, 
    s.datedispatchedfromfacility, s.datereceived, s.datebatchprinted, s.datedispatched, 
    s.dateindividualresultprinted, 

    s.patient, s.caregiverphoneno as caregiver_phone, p.gender, p.initiationdate as initiation_date, p.dob

    FROM viralsamples1214 s
    LEFT JOIN viralpatients1214 p ON p.AutoID=s.patientid

);

CREATE OR REPLACE VIEW old_worksheets_view AS
(
    SELECT id, type as machine_type, lab as lab_id, status as status_id,
    runby, updatedby as uploadedby, reviewedby, review2by as reviewedby2,
    createdby, cancelledby,
    #sortedby, alliquotedby, bulkedby,
    HIQCAPNo as hiqcap_no, Spekkitno as spekkit_no, Rackno as rack_no, Lotno as lot_no, samplepreplotno as
    sample_prep_lot_no, bulklysislotno as bulklysis_lot_no, controllotno as control_lot_no, calibratorlotno as
    calibrator_lot_no, amplificationkitlotno as amplification_kit_lot_no,

    negcontrolresult as neg_control_result, poscontrolresult as pos_control_result,
    negcontrolinterpretation as neg_control_interpretation, poscontrolinterpretation as pos_control_interpretation,
    cdcworksheetno, 
    kitexpirydate, sampleprepexpirydate, bulklysisexpirydate, controlexpirydate, calibratorexpirydate,
    amplificationexpirydate,
    datecut, datereviewed, review2date as datereviewed2, datecancelled, daterun, datecreated as created_at,
    dateupdated as dateuploaded,
    # datesynched,
    synched

    FROM worksheets
);

CREATE OR REPLACE VIEW old_viralworksheets_view AS
(
    SELECT id, type as machine_type, lab as lab_id, status as status_id, calibration,
    runby,  updatedby as uploadedby, reviewedby, review2by as reviewedby2,
    createdby, cancelledby,
    #sortedby, alliquotedby, bulkedby,
    HIQCAPNo as hiqcap_no, Spekkitno as spekkit_no, Rackno as rack_no, Lotno as lot_no, samplepreplotno as
    sample_prep_lot_no, bulklysislotno as bulklysis_lot_no, controllotno as control_lot_no, calibratorlotno as
    calibrator_lot_no, amplificationkitlotno as amplification_kit_lot_no,

    worksheetsampletype as sampletype,

    negcontrolresult as neg_control_result, highposcontrolresult as highpos_control_result, 
    lowposcontrolresult as lowpos_control_result,

    negcontrolinterpretation as neg_control_interpretation, highposcontrolinterpretation as 
    highpos_control_interpretation, lowposcontrolinterpretation as lowpos_control_interpretation,

    negunits as neg_units, hpcunits as hpc_units, lpcunits as lpc_units,

    cdcworksheetno, 
    kitexpirydate, sampleprepexpirydate, bulklysisexpirydate, controlexpirydate, calibratorexpirydate,
    amplificationexpirydate,
    datecut, datereviewed, review2date as datereviewed2, datecancelled, daterun, datecreated as created_at,
    dateupdated as dateuploaded,
    # datesynched,
    synched

    FROM viralworksheets
);

CREATE OR REPLACE VIEW old_cd4_samples_view AS
(
    SELECT s.id, s.worksheet as worksheet_id, s.facility as facility_id, s.labtestedin as lab_id, s.parentid,
    s.AMRSlocation as amrs_location, p.providerid as provider_identifier, 
    # s.serialno as serial_no,
    p.age, s.status as status_id, s.orderno as order_no, s.run, s.action as repeatt, s.receivedstatus, s.rejectedreason,
    s.labcomment, `s`.`THelperSuppressor Ratio` AS THelperSuppressorRatio, s.AVGCD3percentLymph, s.AVGCD3AbsCnt, s.AVGCD3CD4percentLymph, s.AVGCD3CD4AbsCnt,
    s.AVGCD3CD8percentLymph, s.AVGCD3CD8AbsCnt, s.AVGCD3CD4CD8percentLymph, s.AVGCD3CD4CD8AbsCnt, s.CD45AbsCnt, s.result,
    s.approvedby, s.approved2by as approvedby2, s.registeredby as user_id, s.printedby, s.SentEmail as sent_email,
    s.datecollected, s.datereceived, s.datetested, s.datemodified, s.dateapproved, s.dateapproved2, 
    s.dateresultprinted, s.datedispatched,  
    s.flag, s.dateregistered as created_at,


    p.AutoID as original_patient_id, p.names as patient_name, p.medicalrecordno, p.dob, p.gender

    FROM samples s
    LEFT JOIN patients p  ON p.AutoID=s.patient
);

CREATE OR REPLACE VIEW old_cd4_worksheets_view AS
(
    SELECT id, status as status_id, lab as lab_id,
    createdby, Updatedby as uploadedby, reviewedby, review2by as reviewedby2, cancelledby,
    TruCountLotno, AntibodyLotno, MulticheckLowLotno, MulticheckNormalLotno,
    daterun, dateupdated as dateuploaded, datereviewed, review2date as datereviewed2, datecancelled, dumped, flag

    FROM worksheets 
);
