BEGIN NOT ATOMIC
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('no','en','general','No'),
	 ('yes','en','general','Yes'),
	 ('access_denied','en','general','Access denied'),
	 ('token_error','en','general','Token error'),
	 ('params_error','en','general','The requested url has unaccepted params'),
	 ('submit','en','general','Submit'),
	 ('save','en','general','Save'),
	 ('email','en','general','Email'),
	 ('back','en','general','Back'),
	 ('delete','en','general','Delete');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('modify','en','general','Modify'),
	 ('view','en','general','View'),
	 ('add_new','en','general','Add new'),
	 ('confirm','en','general','Confirm'),
	 ('complete','en','general','Complete'),
	 ('country','en','general','Country'),
	 ('language','en','general','Language'),
	 ('author','en','general','Author'),
	 ('visit','en','general','Visit'),
	 ('visits','en','general','Visits list');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('type','en','general','Type'),
	 ('center','en','general','Center'),
	 ('hospital','en','general','Hospital'),
	 ('drug_therapy','en','general','Drug therapy'),
	 ('adverse_events','en','general','Adverse events'),
	 ('agree','en','general','I agree'),
	 ('agree_not','en','general','I do not agree'),
	 ('home','en','general','Home'),
	 ('logout','en','general','Logout'),
	 ('visit_confirm_warning','en','general','Visits data are official only after confirmation, so visits must always be confirmed!<br />Confirmation of this visit will be possible when all mandatory forms are completed. ');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('visit_confirm','en','general','Do you really want to confirm this visit?'),
	 ('other','en','general','Other'),
	 ('no_row_found','en','general','No record was found'),
	 ('visit_create','en','general','Do you really want to create a new visit?'),
	 ('visit_create_warning','en','general','It is not possible to create two visits on the same day.<br>It is not possible to create a visit if another visit is still open. '),
	 ('intro_visit_index','en','general','In this page you can find all the forms to be completed for the visit.<br><br>Please complete all the required forms in order to be able to confirm the visit. Once you complete a form, the red X will turn into a green V. '),
	 ('begin','en','general','Start'),
	 ('date','en','general','Date'),
	 ('informed_consent','en','general','Consent'),
	 ('chart_activity','en','general','Disease activity charts');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('DT_lengthMenu','en','general','Show _MENU_ entries'),
	 ('DT_info','en','general','Showing _START_ to _END_ of _TOTAL_ entries'),
	 ('cancel','en','general','Cancel'),
	 ('patient_code','en','general','Subject code'),
	 ('delete_visit_confirm','en','general','Are you sure you want to delete this visit'),
	 ('form_title','en','general','Form'),
	 ('forms','en','general','Forms'),
	 ('locked','en','general','Locked');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('next','en','general','Next'),
	 ('not_available','en','general','Not available'),
	 ('patients','en','general','Subject list'),
	 ('previous','en','general','Previous'),
	 ('project','en','general','Project'),
	 ('search','en','general','Search'),
	 ('you','en','general','You'),
	 ('days','en','general','Days'),
	 ('from','en','general','from'),
	 ('to','en','general','to');
INSERT INTO language_translation (label_text,languageiso,area_text,`translation`) VALUES
	 ('excellent','en','general','Excellent'),
	 ('very_good','en','general','Very Good'),
	 ('fair','en','general','Fair'),
	 ('poor','en','general','Poor'),
	 ('never','en','general','Never'),
	 ('rarely','en','general','Rarely'),
	 ('sometimes','en','general','Sometimes'),
	 ('often','en','general','Often'),
	 ('always','en','general','Always'),
	 ('good','en','general','Good');
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_index', 'en', 'general', 'Summary'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_census', 'en', 'general', 'Census'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'tools', 'en', 'general', 'Tools'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'last_update', 'en', 'general', 'Last update by %%% on $$$'; 
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'diagnosis', 'en', 'general', 'Diagnosis';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'name', 'en', 'general', 'Name';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'surname', 'en', 'general', 'Surname';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ongoing', 'en', 'general', 'Ongoing';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'export_code', 'en', 'general', 'Export code';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'years', 'en', 'general', 'years';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'age', 'en', 'general', 'Age';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'login', 'en', 'general', 'Login';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient_criteria', 'en', 'general', 'Inclusion & exclusion criteria';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'clear', 'en', 'general', 'Clear';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'filter', 'en', 'general', 'Filter';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'no_selection', 'en', 'general', 'No selection';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'other_specify', 'en', 'general', 'If other, specify';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'screening_failure', 'en', 'general', 'The subject cannot be enrolled due to screening failure';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'end_details', 'en', 'general', ' on %%%<br> for $$$';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'DT_info2', 'en', 'general', 'Showing _MENU_ of _TOTAL_ entries';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'loading', 'en', 'general', 'LOADING';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'study_closed', 'en', 'general', 'The study is locked';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'note', 'en', 'general', 'Note';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'role', 'en', 'general', 'Role';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'enabled', 'en', 'general', 'Enabled';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'disabled', 'en', 'general', 'Disabled';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'users', 'en', 'general', 'Users';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'user', 'en', 'general', 'User';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'phone', 'en', 'general', 'Phone';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'wheel', 'en', 'general', 'Super admin';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'investigator', 'en', 'general', 'Investigator';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'admin', 'en', 'general', 'Administrator';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'pi', 'en', 'general', 'Principal Investigator';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'centers', 'en', 'general', 'Centers';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'class', 'en', 'general', 'Class / Table';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'patient', 'en', 'general', 'Subject';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'all', 'en', 'general', 'All';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'code', 'en', 'general', 'Code';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'day', 'en', 'general', 'Day';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'min', 'en', 'general', 'Min';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'max', 'en', 'general', 'Max';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'add', 'en', 'general', 'Add';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'page', 'en', 'general', 'Page';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'username', 'en', 'auth', 'Username';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'password', 'en', 'auth', 'Password';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'close', 'en', 'general', 'Close';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'ok', 'en', 'general', 'OK';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'send', 'en', 'general', 'Send';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'unlock', 'en', 'general', 'Unlock';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'details', 'en', 'general', 'Details';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'back_to', 'en', 'general', 'Go back to';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'quality_check', 'en', 'general', 'Quality check';
INSERT INTO language_translation(label_text, languageiso, area_text, `translation`)VALUES('translations', 'en', 'general', 'Translations');
INSERT INTO language_translation (label_text, languageiso, area_text, `translation`)VALUES('weight', 'en', 'general', 'Weight (Kg)');
INSERT INTO language_translation (label_text, languageiso, area_text, `translation`)VALUES('height', 'en', 'general', 'Height (cm)');
INSERT INTO language_translation (label_text, languageiso, area_text, `translation`)VALUES('weeks', 'en', 'general', 'Weeks');
INSERT INTO language_translation (label_text, languageiso, area_text, `translation`)VALUES('father', 'en', 'general', 'Father');
INSERT INTO language_translation (label_text, languageiso, area_text, `translation`)VALUES('mother', 'en', 'general', 'Mother');
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'arm', 'en', 'general', 'Arm';
INSERT INTO language_translation (label_text, languageiso, area_text, translation) SELECT 'auto_check', 'en', 'general', 'Automatic check';




END;
