BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'error_present', 'it', 'validation', 'Devi completare tutti i campi correttamente!'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_min_lenght', 'it', 'validation', 'Il testo deve essere lungo almeno {0} caratteri'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_max_lenght', 'it', 'validation', 'Il testo non deve superare i {0} caratteri'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_email', 'it', 'validation', 'Inserisci un indirizzo email valido'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'select_one_option', 'it', 'validation', 'Seleziona un''opzione'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'related_fields', 'it', 'validation', 'Completa i campi relativi'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_date', 'it', 'validation', 'Seleziona una data corretta'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_year', 'it', 'validation', 'Inserisci l''anno con 4 cifre'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_web', 'it', 'validation', 'Inserisci un indirizzo web valido'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_number', 'it', 'validation', 'Questo campo deve essere un numero'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_integer', 'it', 'validation', 'Questo campo deve essere un intero'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_number_minor', 'it', 'validation', 'Questo campo deve essere minore di {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_number_major', 'it', 'validation', 'Questo campo deve essere maggiore di {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_number_minor_equal', 'it', 'validation', 'Questo campo deve essere minore o uguale a {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_number_major_equal', 'it', 'validation', 'Questo campo deve essere maggiore o uguale a {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_minutes', 'it', 'validation', 'Ora e minuti devono essere nel formato hh:mm'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'link_expired', 'it', 'validation', 'Il link ?? scaduto'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password_format', 'it', 'validation', 'La password deve contenere almeno {0} caratteri, una lettera maiuscola, una lettera minuscola, un numero ed un carattere speciale'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'match_password', 'it', 'validation', 'Le passwords non corrispondono'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password_equal_prev', 'it', 'validation', 'La nuova password non pu?? essere uguale alla vecchia password'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password_wrong', 'it', 'validation', 'La password corrente ?? errata'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'warning_mobile', 'it', 'validation', 'Non hai inserito il numero di telefono. Lo staff medico pu?? aver bisogno di contattarti.'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'dates_incongruence', 'it', 'validation', 'C''?? un''incongruenza tra le date'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_precise_lenght', 'it', 'validation', 'Il testo deve essere lungo {0} caratteri'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'delete_confirmation', 'it', 'validation', 'Sei sicuro di eliminare {0} ?'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'required_field', 'it', 'validation', 'Il campo non pu?? essere vuoto'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'disabled_submit', 'it', 'validation', 'Ops! Ci sono errori nella pagina! Le funzionalit?? sono disabilitate, contatta l''amministratore'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'wrong_format', 'it', 'validation', 'Questo campo non rispetta il formato corretto'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_emails', 'it', 'validation', 'Inserisci validi indirizzi email separati da virgola'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_date_minor', 'it', 'validation', 'La data deve essere dopo il {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_date_major', 'it', 'validation', 'La data deve essere prima del {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unsaved_discarded', 'it', 'validation', 'Tutte le modifiche non salvane andranno perse'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_date_minor_equal', 'it', 'validation', 'La data deve essere dopo o uguale al {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_date_major_equal', 'it', 'validation', 'La data deve essere prima o uguale al {0}'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'text_empty_spaces', 'it', 'validation', 'Gli spazi non sono consentiti all''inizio ed alla fine di questo campo'; 

END;
