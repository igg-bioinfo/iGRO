
BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forgot_password', 'en', 'auth', 'Forgot your password?';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password_retrieve', 'en', 'auth', 'Retrieve your password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'insert_mail', 'en', 'auth', 'Insert your email';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forgot_pw_confirm', 'en', 'auth', 'Please check your email account for further instructions. If you have not received any email, please check you provided the correct email address.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pw_expired_confirm', 'en', 'auth', 'Your password is expired. Please check your email account for further instructions.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pw_equal', 'en', 'auth', 'Password is equal to the previous one. New password must be different from the previous one.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'link_expired', 'en', 'auth', 'This link expired.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_password', 'en', 'auth', 'Insert new password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_password2', 'en', 'auth', 'Repeat your password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'reset_pw', 'en', 'auth', 'Change your password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'reset_pw_confirm', 'en', 'auth', 'Your password was successfully changed. Please back to login page and use your new credentials.';






INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forgot_password', 'it', 'auth', 'Password dimenticata?';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password_retrieve', 'it', 'auth', 'Recupera la password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'insert_mail', 'it', 'auth', 'Inserisci la tua email';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'forgot_pw_confirm', 'it', 'auth', 'Controlla la tua email per ulteriori istruzioni. Se non hai ricevuto nessuna email, controlla che l''email sia scritta correttamente.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pw_expired_confirm', 'it', 'auth', 'La tua password è scaduta. Controlla il tuo indirizzo email per cambiarla.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pw_equal', 'it', 'auth', 'La password è uguale alla precedente.La nuova password deve essere differente.';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'link_expired', 'it', 'auth', 'Il link è scaduto.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_password', 'it', 'auth', 'Inserisci una nuova password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_password2', 'it', 'auth', 'Ripeti la password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'reset_pw', 'it', 'auth', 'Cambia la password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'reset_pw_confirm', 'it', 'auth', 'la password è stata cambiata correttamente. Torna alla pagina della login e usa le tue nuove credenziali.';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'center_pw', 'en', 'auth', 'Center password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'browser_warning', 'en', 'auth', 'The following browsers are currently supported and their use is mandatory';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'center_pw', 'it', 'auth', 'Parssword del Centro';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'browser_warning', 'it', 'auth', 'Sono attualmente supportati i seguenti browsers ed il loro uso è obbligatorio';



END;

