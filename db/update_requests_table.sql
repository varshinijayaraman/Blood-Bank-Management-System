USE blood_bank;

-- Modify the requests table to make hospital and doctor_contact nullable
ALTER TABLE requests 
MODIFY COLUMN hospital VARCHAR(100) NULL,
MODIFY COLUMN doctor_contact VARCHAR(15) NULL; 