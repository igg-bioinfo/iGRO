BEGIN NOT ATOMIC
INSERT INTO page_template (file_name,page_url,id_area,need_login) VALUES
	 ('login.php','login',1,0),
	 ('login.php','login',2,0),
	 ('area_admin/patient_list.php','patients',1,1),
	 ('area_investigator/patient_list.php','patients',2,1),
	 ('area_investigator/patient_census.php','patient_census',2,1),
	 ('patient_status.php','patient_status',1,1),
	 ('patient_status.php','patient_status',2,1),
	 ('patient_index.php','patient_index',1,1),
	 ('patient_index.php','patient_index',2,1),
	 ('ajax.php','ajax',1,1);
INSERT INTO page_template (file_name,page_url,id_area,need_login) VALUES
	 ('ajax.php','ajax',2,1),
	 ('patient_criteria.php','patient_criteria',1,1),
	 ('patient_criteria.php','patient_criteria',2,1),
	 ('visit_list.php','visits',1,1),
	 ('visit_list.php','visits',2,1),
	 ('visit_edit.php','visit',1,1),
	 ('visit_edit.php','visit',2,1),
	 ('visit_index.php','visit_index',1,1),
	 ('visit_index.php','visit_index',2,1),
	 ('crf/form_manager.php','form',1,1),
	 ('crf/form_manager.php','form',2,1);
INSERT INTO page_template (file_name,page_url,id_area,need_login) VALUES
	 ('area_admin/user_list.php','users',1,1),
	 ('area_admin/user_edit.php','user',1,1),
	 ('login.php','login',3,0),
	 ('area_superadmin/home.php','home',3,1),
	 ('area_admin/user_list.php','users',3,1),
	 ('area_admin/user_edit.php','user',3,1),
	 ('area_superadmin/center_list.php','centers',3,1),
	 ('area_superadmin/center_edit.php','center',3,1),
	 ('area_superadmin/form_list.php','forms',3,1),
	 ('area_superadmin/form_edit.php','form',3,1);
INSERT INTO page_template (file_name,page_url,id_area,need_login) VALUES
	 ('area_superadmin/visit_type_list.php','visit_types',3,1),
	 ('area_superadmin/visit_type_edit.php','visit_type',3,1),
	 ('ajax.php','ajax',3,1),
	 ('area_superadmin/visit_form_edit.php','visit_forms',3,1),
	 ('area_superadmin/field_edit.php','field',3,1),
	 ('area_superadmin/field_list.php','fields',3,1),
	 ('visit_lock.php','visit_lock',1,1),
	 ('visit_lock.php','visit_lock',2,1),
	 ('visit_output.php','output',1,1),
	 ('visit_output.php','output',2,1);
	 
INSERT INTO page_template (file_name, page_url, id_area, need_login)VALUES('area_superadmin/translation_list.php', 'translations', 3, 1);
	END; 
	
	
