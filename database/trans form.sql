
BEGIN NOT ATOMIC

INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'fields', 'en', 'form', 'Fields';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'field', 'en', 'form', 'Field';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'group', 'en', 'form', 'Group';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'order', 'en', 'form', 'Order';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'required', 'en', 'form', 'Required';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'fields', 'it', 'form', 'Campi';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'field', 'it', 'form', 'Campo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'group', 'it', 'form', 'Gruppo';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'order', 'it', 'form', 'Ordine';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'required', 'it', 'form', 'Obbligatorio';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'extra', 'en', 'form', 'Json';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'table', 'en', 'form', 'Tabella';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'extra', 'it', 'form', 'Json';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'table', 'it', 'form', 'Tabella';


END;

