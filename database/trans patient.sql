
BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_name', 'en', 'patient', 'Subject name or initial'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_lastname', 'en', 'patient', 'Subject surname or initial'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'sex', 'en', 'patient', 'Sex'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'male', 'en', 'patient', 'Male'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'female', 'en', 'patient', 'female';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_birth', 'en', 'patient', 'Date of birth';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'country_birth', 'en', 'patient', 'Country of birth';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'country_birth_other', 'en', 'patient', 'Region of birth';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_onset', 'en', 'patient', 'Date onset';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_diagnosis', 'en', 'patient', 'Date diagnosis';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'national_unique_id', 'en', 'patient', 'National unique ID';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'national_na', 'en', 'patient', 'Check this option if the National unique ID is not available';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_first', 'en', 'patient', 'First visit at your center';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_last', 'en', 'patient', 'Last visit at your center';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'fullname', 'en', 'patient', 'Full name';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_followed', 'en', 'patient', 'Subject is followed';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_discontinued', 'en', 'patient', 'Subject is discontinued';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_dead', 'en', 'patient', 'Subject is dead';


INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_name', 'it', 'patient', 'Nome del paziente o anche la lettera iniziale'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_lastname', 'it', 'patient', 'Cognome del paziente o anche la lettera iniziale';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'sex', 'it', 'patient', 'Genere'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'male', 'it', 'patient', 'Maschio'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'female', 'it', 'patient', 'Femmina'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_birth', 'it', 'patient', 'Data di nascita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'country_birth', 'it', 'patient', 'Paese di nascita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'country_birth_other', 'it', 'patient', 'Provincia di nascita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_onset', 'it', 'patient', 'Data dell''esordio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_diagnosis', 'it', 'patient', 'Data della diagnosi';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'national_unique_id', 'it', 'patient', 'Codice fiscale';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'national_na', 'it', 'patient', 'Spunta questa opzione se il codice fiscale non è disponibile';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_first', 'it', 'patient', 'Data della visita per l''arruolamento';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_last', 'it', 'patient', 'Ultima visita al tuo centro';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'fullname', 'it', 'patient', 'Nome e cognome';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_followed', 'it', 'patient', 'Il paziente è attualmente seguito dal tuo centro';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_discontinued', 'it', 'patient', 'Il paziente è uscito dallo studio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_dead', 'it', 'patient', 'Il paziente è morto';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ethnicity', 'it', 'patient', 'Etnia';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ethnicity', 'en', 'patient', 'Ethnicity';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'caucasic', 'it', 'patient', 'Caucasico';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ispanic', 'it', 'patient', 'Ispanico';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'afro', 'it', 'patient', 'Afro-americano';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'asiatic', 'it', 'patient', 'Asiatico';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'caucasic', 'en', 'patient', 'Caucasic';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ispanic', 'en', 'patient', 'Ispanic';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'afro', 'en', 'patient', 'Afro-american';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'asiatic', 'en', 'patient', 'Asiatic';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_diagnosis', 'it', 'patient', 'Il paziente è affetto da <b>%%%</b> ';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_diagnosis', 'en', 'patient', 'The subject is affected by <b>%%%</b> ';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_new', 'it', 'patient', 'Nuovo paziente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_new', 'en', 'patient', 'New subject';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_form', 'en', 'patient', 'Discontinuation form';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_form', 'it', 'patient', 'Scheda di uscita dallo studio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_reason', 'en', 'patient', 'Reason of discontinuation';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_date', 'en', 'patient', 'Date of discontinuation';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_reason', 'it', 'patient', 'Causa di uscita dallo studio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_date', 'it', 'patient', 'Data di uscita dallo studio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'death', 'en', 'patient', 'Date';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'consent_retired', 'en', 'patient', 'Informed consent withdrawal';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'lost_followup', 'en', 'patient', 'Lost follow-up';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ea_major', 'en', 'patient', 'Major adverse events';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'treatment_failure', 'en', 'patient', 'Treatment failure';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'death', 'it', 'patient', 'Morte';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'consent_retired', 'it', 'patient', 'Ritiro del consenso per la partecipazione allo studio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'lost_followup', 'it', 'patient', 'Paziente perso al followup';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ea_major', 'it', 'patient', 'Comparsa di eventi avversi maggiori';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'treatment_failure', 'it', 'patient', 'Non aderenza alla terapia';

END;
