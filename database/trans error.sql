
BEGIN NOT ATOMIC

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_page', 'en', 'error', 'Page does not exist';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'post', 'en', 'error', 'Test error after post';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'query', 'en', 'error', 'Error in query execution';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_username', 'en', 'error', 'username not valid';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_center_pw', 'en', 'error', 'center password not valid';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_criteria', 'en', 'error', 'Criteria are not satisfied';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_disc_error', 'en', 'error', 'Date of discontinuation is unacceptable';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'subject_used', 'en', 'error', 'Subject ID is already used';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_start_error', 'en', 'error', 'Date start is unacceptable';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'physician_confirm', 'en', 'error', 'Only a physician can confirm this visit.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'all_confirmed', 'en', 'error', 'All visits must be confirmed';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_validation', 'en', 'error', 'Form validation error';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_page_n', 'en', 'error', 'Incorrect page number assignment';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_field_n', 'en', 'error', 'Total number of fields drawn is incorrect';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_field_n_page', 'en', 'error', 'No fields found for the current page';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'double_field', 'en', 'error', 'Same field used twice in drawing';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_field', 'en', 'error', 'Field for drawing not found';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_main_field', 'en', 'error', 'No other main field found';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'too_records', 'en', 'error', 'Too many records returned';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_resource', 'en', 'error', 'The requested resource is not available';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'server_error', 'en', 'error', 'Internal server error';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_page', 'it', 'error', 'La pagina non esiste';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'post_error', 'it', 'error', 'Esecuzione del post fallita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'query', 'it', 'error', 'Interrogazione del database fallita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_username', 'it', 'error', 'Utente non presente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_center_pw', 'it', 'error', 'La password del centro non è corretta';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_criteria', 'it', 'error', 'I criteria non sono stati soddisfatti';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_disc_error', 'it', 'error', 'La data di discontinuazione è incoerente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'subject_used', 'it', 'error', 'Il codice paziente è già utilizzato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_start_error', 'it', 'error', 'La data di inizio è incoerente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'physician_confirm', 'it', 'error', 'Solo un medico può confermare la visita.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'all_confirmed', 'it', 'error', 'Tutte le visite devono essere confermate';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_validation', 'it', 'error', 'Errore di validazione della scheda';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_page_n', 'it', 'error', 'Numero pagina errato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_field_n', 'it', 'error', 'Il numero totale di campi non è corretto';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'bad_field_n_page', 'it', 'error', 'Nessun campo per questa pagina';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'double_field', 'it', 'error', 'Lo stesso campo è renderizzato due volte';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_field', 'it', 'error', 'Campo non trovato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_main_field', 'it', 'error', 'Campo primario non trovato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'too_records', 'it', 'error', 'Troppi risultati riscontrati';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_resource', 'it', 'error', 'La risorsa richiesta non è disponibile';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'server_error', 'it', 'error', 'Errore interno del server';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_privilege', 'en', 'error', 'Insufficient privileges to fullfill the request';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'file_exists', 'en', 'error', 'File already exists';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_privilege', 'it', 'error', 'Privilegi insufficienti per adempiere la richiesta';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'file_exists', 'it', 'error', 'Il file esiste già';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_criteria_modify', 'en', 'error', 'After criteria confirmation, you won''t be able to modify the subject census';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_criteria_locked', 'en', 'error', 'If you want to modify the criteria, no visit should be present for this subject';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_criteria_modify', 'it', 'error', 'Dopo la conferma dei criteri non sarà più possibile modificare l''anagrafica del paziente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_criteria_locked', 'it', 'error', 'Se vuoi modificare i criteri, non devono essere presenti visite per questo paziente';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_census_modify', 'en', 'error', 'After criteria confirmation, you won''t be able to modify these subject data';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_census_locked', 'en', 'error', 'If you want to modify these subject data, no criteria should be present for this subject';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_census_modify', 'it', 'error', 'Questi dati relativi al paziente vanno compilati attentamente in quanto dopo aver confermato i criteri non sarà più possibile modificarli';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) 
SELECT 'ins_census_locked', 'it', 'error', 'Se vuoi modificare l''anagrafica e/o la diagnosi, è necessario eliminare i criteri di inclusione / esclusione ed in seguito reinserirli';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock_requested', 'en', 'error', 'Unlock visit request sent';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock_requested', 'it', 'error', 'Richiesta di sblocco visita inviata';

END;

