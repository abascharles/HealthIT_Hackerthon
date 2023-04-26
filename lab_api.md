## LAB API

[Click here](http://lab-2.test.nascop.org/download/api) to get the below post routes as a **POSTMAN** collection complete with all the fields you need. Open this file using **POSTMAN**. If you do not have **POSTMAN** installed, you can download it by [clicking here.](https://www.getpostman.com/downloads/)


All links are **POST** requests. All post requests are validated using a header called **apikey**. The postman collection has already added them.
Open the link below in **POSTMAN** and then set the request to **POST** 
> Get data by request type [click here](http://lab.test.nascop.org/api/function)
Fields
- **test** - 1 for eid, 2 for vl **required**
- **start_date** - (required when *end_date* is set). It filters using the date collected.
- **end_date** - *greater than or equal to **start_date**)*
- **date_dispatched_start** - (required when *date_dispatched_end* is set)
- **date_dispatched_end** - *(greater than or equal to **date_dispatched_start**)*
- **patient_id** - *Either a single patient_id or a comma separated list of patient ids with no spaces. Patient id is hei number for eid and ccc number for vl*
- **facility_code** - *The five digit MFL code* Either a single MFL code or a comma separated list of MFL codes with no spaces.*
- **order_numbers** - *Either a single mrs order number or a comma separated list of mrs order numbers with no spaces.*
- **location** - *AMRS location*
- **dispatched** - *Boolean* When true, only samples that have been dispatched will be returned.

This link is paginated i.e. only 50 results at a time will be returned. The return data has a field called next_page_url and other links to help you get all the results. There is also other useful data such as the total results found.

These are the fields that you should expect back:
- **id** - This is the system id of the sample.
- **order_number** - This is the order number.
- **provider_identifier** - AMPATH sites only.
- **AMRs_location** - AMPATH sites only.
- **facility_code** - The facility MFL Code.
- **patient** - The patient's CCC/HEI number.
- **full_names** - The patient's name.
- **date_collected** - The date the sample was collected.
- **date_received** - The date the sample was received.
- **date_tested** - The date the sample was tested.
- **date_dispatched** - The date the sample was dispatched.
- **interpretation** - The raw result from the machine.
- **result** - The result of the test. 
- **sample_status** - The status of the sample. It can either be Complete, Incomplete or Rejected.
- **rejected_reason** - If the sample is rejected, this attribute will be present and will contain the reason why the sample was rejected.

---
For the links below, if the order number exists, it will not save it again.
- Post incomplete eid request (EID order) [eid](http://lab.test.nascop.org/api/eid)
- Post complete eid request [eid](http://lab.test.nascop.org/api/eid_complete)
- Post incomplete vl request (Viralload order) [vl](http://lab.test.nascop.org/api/vl)
- Post complete vl request [vl](http://lab.test.nascop.org/api/vl_complete)

---
For the last 4 links, the following fields are common to all
 - **dob** - **required**
 - **datecollected** -  **required**  *(greater than or equal to **dob**)*
 - **patient_identifier** -  **required** (ccc_no for vl and hei number for eid)
 - **mflCode** -  **required**
 - **sex** -  **required** (1 for male, 2 for female, 3 for unknown)
 - **lab** - (This is the lab tested in.) *If the lab is not filled it will be set to the lab where the sample is being sent. It is however mandatory for complete requests.*

---
For the last 4 links, the following fields are common to all requests where you want the patient to receive sms notfications
 - **patient_phone_no** -  *(format 2547???????)*
 - **preferred_language** - *(1 for English, 2 for Kiswahili)*

---
The following fields are common to complete requests 
- **receivedstatus** - **required**
- **datereceived** - **required**  *(greater than or equal to **datecollected**)*
- **rejectedreason** - *required if received status is 2*
- **datetested** - *required if received status is 1*  *(greater than or equal to **datereceived**)*
- **result** - *required if received status is 1*
- **datedispatched** - *required if received status is 1*  *(greater than or equal to **datetested**)*
- **amrs_location** - *the amrs location of the sample*
- **provider_identifier** - *the provider identifier of the sample*
- **order_no** - *this is the order number from mrs*
- **patient_name** - *The name of the patient.*
- **specimenlabelID** - *for use by the lab*
- **editted** - *indicates whether the record is an update of an existing record*

` Editted may be removed so that editted records are sent to another route`

---
The following fields are common to eid requests
 - **entry_point** - **required**
 - **feeding** - **required**
 - **spots** - **integer**
 - **regimen** - **required** *The infant prophylaxis*
 - **mother_prophylaxis** - **required** *The pmtct intervention*
 - **mother_age** - **integer**
 - **ccc_no** - *(The ccc number of the mother)*
 - **mother_last_result** - *(The most recent vl of the mother)*
 - **hiv_status** - **integer** *The HIV status of the mother. It was removed from the form but the system still supports it.*
 - **pcrtype** - **required**  *(**integer between 1 and 5**)*
 - **redraw** - *(Fill with any non zero integer if the sample is a redraw)*
 - **enrollment_ccc_no** - *(ccc number of the infant if he/she is already enrolled)*

---
The following fields are common to vl requests
 - **initiation_date** - *(date inititated on treatment)*
 - **dateinitiatedonregimen** - *(date inititated on current regimen)*
 - **prophylaxis** - **Required** Give the 4 character code as appears on the form.
 - **sampletype** - **Required**
 - **justification** - **Required**
 - **pmtct** - *(**Required** if sex is **2** i.e. **female**. 1 for pregnant, 2 for breast-feeding and 3 is for none of the above)*
 - **recency_number** - *The recency number of the sample*


---
**NB:**
>`All date fields must follow the YYYY-MM-DD format and be less than or equal to today.`
>`All fields are case sensitive`



