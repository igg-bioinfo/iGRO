BEGIN NOT ATOMIC

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'recipients', 'en', 'email', 'Recipients';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'recipients', 'it', 'email', 'Destinatari';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'object', 'en', 'email', 'Subject';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'object', 'it', 'email', 'Oggetto';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'body', 'en', 'email', 'Message';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'sender', 'en', 'email', 'Sender';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'body', 'it', 'email', 'Messaggio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'sender', 'it', 'email', 'Mittente';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_pw_obj', 'en', 'email', 'Personal password management';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_pw_msg', 'en', 'email', 'Dear %%%,<br/><br/>You are receiving this email because you required a new password to access our website.<br/><br/>Here is the link to change your password:<br><br><a href="$$0$$">Click here to create your new password</a><br><br>Please consider that the link above expires in one hour<br><br>Kind regards,<br>the administrator';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'acc_suspended_obj', 'en', 'email', 'Your account has been suspended';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'acc_suspended_msg', 'en', 'email', 'Dear %%%,<br><br>The password has been entered incorrectly several times.<br><br>Your account $$0$$ has been temporarily suspended for security reasons.<br>Please contact us to reactivate the account.<br><br>Kind regards,<br>the administrator';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_pw_obj', 'it', 'email', 'Gestione della password personale';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'new_pw_msg', 'it', 'email', 'Gentile %%%,<br/><br/>Hai ricevuto questa email a seguito della richiesta di una nuova password per poter accedere al nostro sito.<br/><br/>Ecco il link per cambiare la tua password:<br><br><a href="$$0$$">Clicca qui per generare una nuova password</a><br><br>Tieni in considerazione che il link scade dopo un''ora dall''invio<br><br>Cordiali saluti,<br>l''amministratore';

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'acc_suspended_obj', 'it', 'email', 'Il tuo account è stato sospeso';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'acc_suspended_msg', 'it', 'email', 'Gentile %%%,<br><br>Sono stati effettuati parecchi tentativi di accesso al tuo account sul nostro sito.<br><br>Il tuo account $$0$$ è stato temporaneamente sospeso per motivi di sicurezza.<br>Per favore contattaci per riattivare  l''account.<br><br>Cordiali saluti,<br>l''amministratore';


END;

