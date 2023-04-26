USE vl_kemri2;
UPDATE viralsamples SET highpriority=0 WHERE highpriority IS NULL;
UPDATE viralsamples SET inputcomplete=1 WHERE inputcomplete IS NULL;
UPDATE viralsamples SET batchcomplete=0 WHERE batchcomplete IS NULL;
UPDATE viralsamples SET site_entry=0 WHERE site_entry IS NULL;
UPDATE viralsamples SET sent_email=0 WHERE sent_email IS NULL;
UPDATE viralsamples SET age2=0 WHERE age2 IS NULL;
UPDATE viralsamples SET pmtct=3 WHERE pmtct IS NULL;
UPDATE viralsamples SET parentid=0 WHERE parentid IS NULL;
UPDATE viralsamples SET synched=0 WHERE synched IS NULL;

USE eid_kemri2;

