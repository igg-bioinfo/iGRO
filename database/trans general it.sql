BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('no','it','general','No'),
	 ('yes','it','general','Sì'),
	 ('access_denied','it','general','Accesso negato'),
	 ('token_error','it','general','Errore del token'),
	 ('params_error','it','general','L''indirizzo richiesto ha parametri non corretti'),
	 ('submit','it','general','Conferma'),
	 ('save','it','general','Salva'),
	 ('email','it','general','Email'),
	 ('back','it','general','Indietro'),
	 ('delete','it','general','Elimina');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('modify','it','general','Modifica'),
	 ('view','it','general','View'),
	 ('add_new','it','general','Crea nuovo'),
	 ('confirm','it','general','Conferma'),
	 ('complete','it','general','Completa'),
	 ('country','it','general','Paese'),
	 ('language','it','general','Lingua'),
	 ('author','it','general','Autore'),
	 ('visit','it','general','Visita'),
	 ('visits','it','general','Visite');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('type','it','general','Tipo'),
	 ('center','it','general','Centro'),
	 ('hospital','it','general','Ospedale'),
	 ('drug_therapy','it','general','Terapia del farmaco'),
	 ('adverse_events','it','general','Eventi avversi'),
	 ('agree','it','general','Acconsento'),
	 ('agree_not','it','general','Non acconsento'),
	 ('home','it','general','Home'),
	 ('logout','it','general','Logout'),
	 ('visit_confirm_warning','it','general','I dati delle visite sono ufficiali solo dopo la conferma!<br />La conferma della visita sarà possibile quando tutti i dati obbligatori saranno compilati. ');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('visit_confirm','it','general','Sei sicuro di voler confermare questa visita?'),
	 ('other','it','general','Altro'),
	 ('no_row_found','it','general','Nessun dato è stato trovato'),
	 ('visit_create','it','general','Sei sicuro di voler creare una nuova visita?'),
	 ('visit_create_warning','it','general','Non è possibile creare due visite per lo stesso giorno.<br>Non è possibile creare una nuova visita quando un''altra è ancora aperta. '),
	 ('intro_visit_index','it','general','In questa pagina sono presenti tutte le schede da completare per questa visita.<br><br>Compila tutte le schede obbligatorie per poter confermare la visita. Una volta conclusa una scheda, la X rossa diventerà una V verde. '),
	 ('begin','it','general','Inizia'),
	 ('date','it','general','Data'),
	 ('informed_consent','it','general','Consenso'),
	 ('chart_activity','it','general','Grafici di attività della malattia');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('DT_lengthMenu','it','general','Mostra _MENU_ risultati'),
	 ('DT_info','it','general','Mostra da _START_ a _END_ per un totale di _TOTAL_ risultati'),
	 ('cancel','it','general','Annulla'),
	 ('patient_code','it','general','Codice paziente'),
	 ('delete_visit_confirm','it','general','Sei sicuro di voler eliminare questa visita?'),
	 ('form_title','it','general','Scheda'),
	 ('forms','it','general','Schede'),
	 ('locked','it','general','Bloccato');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('next','it','general','Avanti'),
	 ('not_available','it','general','Non disponibile'),
	 ('patients','it','general','Elenco pazienti'),
	 ('previous','it','general','Indietro'),
	 ('project','it','general','Progetto'),
	 ('search','it','general','Cerca'),
	 ('you','it','general','Tu'),
	 ('days','it','general','Giorni'),
	 ('from','it','general','Da'),
	 ('to','it','general','A');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('excellent','it','general','Eccellente'),
	 ('very_good','it','general','Molto buono'),
	 ('fair','it','general','Bene'),
	 ('poor','it','general','Scarso'),
	 ('never','it','general','Mai'),
	 ('rarely','it','general','Raramente'),
	 ('sometimes','it','general','Talvolta'),
	 ('often','it','general','Spesso'),
	 ('always','it','general','Sempre'),
	 ('good','it','general','Buono');
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_index', 'it', 'general', 'Riepilogo dati'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_census', 'it', 'general', 'Dati del paziente'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'tools', 'it', 'general', 'Strumenti'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'last_update', 'it', 'general', 'Ulitmo aggiornamento da parte di %%%, il $$$'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'diagnosis', 'it', 'general', 'Diagnosi';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'name', 'it', 'general', 'Nome';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'surname', 'it', 'general', 'Cognome';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ongoing', 'it', 'general', 'Seguito';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'export_code', 'it', 'general', 'Codice di esportazione';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'years', 'it', 'general', 'anni';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'age', 'it', 'general', 'Età';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'login', 'it', 'general', 'Autenticazione';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_criteria', 'it', 'general', 'Criteri di inclusione e esclusione';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'clear', 'it', 'general', 'Azzera';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'filter', 'it', 'general', 'Filtra';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_selection', 'it', 'general', 'Nessuna selezione';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'other_specify', 'it', 'general', 'Se altro, specifica';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'screening_failure', 'it', 'general', 'Il paziente non è arruolabile per mancata adempienza dei criteri di inclusione e/o esclusione';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_details', 'it', 'general', ' il %%%<br> per $$$';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'DT_info2', 'it', 'general', 'Mostra _MENU_ risultati su un totale di _TOTAL_';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'loading', 'it', 'general', 'CARICAMENTO';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'study_closed', 'it', 'general', 'Lo studio è concluso';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'note', 'it', 'general', 'Nota';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'role', 'it', 'general', 'Ruolo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'enabled', 'it', 'general', 'Abilitato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'disabled', 'it', 'general', 'Disabilitato';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'users', 'it', 'general', 'Elenco utenti';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'user', 'it', 'general', 'Utente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'phone', 'it', 'general', 'Telefono';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'wheel', 'it', 'general', 'Super admin';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'investigator', 'it', 'general', 'Ricercatore';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'admin', 'it', 'general', 'Amministratore';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pi', 'it', 'general', 'Ricercatore Capo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'centers', 'it', 'general', 'Elenco centri';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'class', 'it', 'general', 'Classe / Tabella';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient', 'it', 'general', 'Paziente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'all', 'it', 'general', 'Tutti';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'code', 'it', 'general', 'Codice';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'day', 'it', 'general', 'Giorno';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'min', 'it', 'general', 'Minimo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'max', 'it', 'general', 'Massimo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'add', 'it', 'general', 'Aggiungi';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'page', 'it', 'general', 'Pagina';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'username', 'it', 'auth', 'Utente';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password', 'it', 'auth', 'Password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'close', 'it', 'general', 'Chiudi';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ok', 'it', 'general', 'OK';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'send', 'it', 'general', 'Invia';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock', 'it', 'general', 'Sblocca';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'details', 'it', 'general', 'Dettagli';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'back_to', 'it', 'general', 'Torna a';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'quality_check', 'it', 'general', 'Controllo di qualità';




END;
