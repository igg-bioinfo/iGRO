/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    config.language = 'en';
    //config.uiColor = '#00DC6E';
    config.toolbar = [
		{ name: 'document', groups: [ 'document', 'undo' ], items: [ 'Undo', 'Redo' ] },
		{ name: 'clipboard', groups: [ 'clipboard' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord' ] },
		{ name: 'insert',  groups: [ 'links', 'items','list' ], 
                    items: ['Link', 'Unlink', '-', 'NumberedList', 'BulletedList', '-', 'Image', 'Table', 'SpecialChar' ] }, 
		{ name: 'tools', groups: [ 'mode' ], items: [ 'Maximize', 'Source' ] },
		'/',
		{ name: 'styles', items: [ 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] }
	];
    /*
    config.toolbar = [
		{ name: 'document', groups: [ 'document', 'undo' ], items: [ 'Preview', 'Print', '-', 'Undo', 'Redo'  ] },
		{ name: 'clipboard', groups: [ 'clipboard' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'SelectAll', 'Find' ] },
		{ name: 'insert',  groups: [ 'links', 'items' ], items: [ 'Link', 'Unlink', '-', 'Image', 'Table', 'Smiley', 'SpecialChar' ] }, 
		{ name: 'tools', groups: [ 'mode' ], items: [ 'Maximize', 'Source' ] },
		'/',
		{ name: 'styles', items: [ 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'align' ], 
			items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
	];
    */
};
