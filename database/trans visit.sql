
BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_visit_not_allowed', 'en', 'visit', 'You cannot insert a new visit because:';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_visit', 'en', 'visit', 'New visit';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'last_visit_not_confirmed', 'en', 'visit', 'Last visit should be confirmed';



INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_visit_not_allowed', 'it', 'visit', 'Non è possibile creare una nuova visita perchè:';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_visit', 'it', 'visit', 'Crea una nuova visita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'last_visit_not_confirmed', 'it', 'visit', 'L''ultima visita deve essere confermata';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_type', 'en', 'visit', 'No visit type is available';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_type', 'it', 'visit', 'Nessun tipo di visita è disponibile';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_last', 'en', 'visit', 'Date %%% is before the last visit date';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_last', 'it', 'visit', 'La data %%% è prima dell''ultima visita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_prev', 'en', 'visit', 'Date %%% is before the last previous visit date';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_prev', 'it', 'visit', 'La data %%% è prima della visita precedente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_after_next', 'en', 'visit', 'Date %%% is after the next visit date';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_after_next', 'it', 'visit', 'La data %%% è dopo il range della prossima visita';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_first', 'en', 'visit', 'Date %%% is before the enrollment visit';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'date_before_first', 'it', 'visit', 'La data %%% è prima della visita di arruolamento';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'baseline', 'en', 'visit', 'Baseline';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'baseline', 'it', 'visit', 'Visita basale';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'always_show', 'en', 'visit', 'Always shown';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'extra_visit', 'en', 'visit', 'Extra visit';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'always_show', 'it', 'visit', 'Mostra sempre';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'extra_visit', 'it', 'visit', 'Visita straordinaria';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'optional', 'en', 'visit', 'Optional';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'confirm_group', 'en', 'visit', 'Center data confirmation';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_locked', 'en', 'visit', 'This visit has been locked by <b>{0}</b> on <b>{1}</b>';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'can_confirm', 'en', 'visit', 'All the required data have been entered. It is possible to confirm this visit.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forms_uncompleted', 'en', 'visit', ' forms to be completed';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_unlocked', 'en', 'visit', 'This visit has been unlocked by <b>{0}</b> on <b>{1}</b>';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'optional', 'it', 'visit', 'Opzionale';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'confirm_group', 'it', 'visit', 'Conferma dei dati';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_locked', 'it', 'visit', 'Questa visita è stata confermata da <b>{0}</b>, il <b>{1}</b>';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'can_confirm', 'it', 'visit', 'Tutti i dati obbligatori sono stati inseriti. E'' possibile confermare la visita.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forms_uncompleted', 'it', 'visit', ' schede obbligatorie da completare';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'visit_unlocked', 'it', 'visit', 'Questa visita è stata sbloccata da <b>{0}</b>, il <b>{1}</b>';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'cant_lock_prev', 'en', 'visit', 'This visit cannot be locked because there''s at least one previous visit already unlocked';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'cant_lock_prev', 'it', 'visit', 'Questa visita non può essere confermata perchè c''è almeno una visita precedente già sbloccata';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'cant_unlock_next', 'en', 'visit', 'This visit cannot be unlocked because there''s at least one next visit still locked';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'cant_unlock_next', 'it', 'visit', 'Questa visita non può essere sbloccata perchè c''è almeno una visita sucessiva ancora confermata';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock_question', 'en', 'visit', 'Are you sure you want to unlock this visit?';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock_question', 'it', 'visit', 'Sei sicuro di voler sbloccare questa visita?';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'lock_question', 'en', 'visit', 'Are you sure you want to confirm this visit?';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'lock_question', 'it', 'visit', 'Sei sicuro di voler confermare questa visita?';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'alert_email_unlock', 'en', 'visit', 'After the confirmation, you will have to send an automated email to unlock the visit and modify the data entered.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'alert_email_unlock', 'it', 'visit', 'Dopo la conferma, sarà necessario mandare una email automatizzata per sbloccare la visita e modificare i dati inseriti';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'output', 'en', 'visit', 'Output';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'output', 'it', 'visit', 'Output';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'randomization', 'en', 'visit', 'Randomization';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'randomization', 'it', 'visit', 'Randomizzazione';



END;
