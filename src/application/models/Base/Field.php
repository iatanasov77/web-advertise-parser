<?php

class Model_Base_Field extends Doctrine_Record {
	
	public function setTableDefinition()
    {
        $this->setTableName('fields');
        
        $this->hasColumn('id', 'integer', 5, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('fieldset', 'string', 100);
        $this->hasColumn('caption', 'string', 200);
        $this->hasColumn('type', 'string', 20);
        
    }
	//======================================================================================================
    
    public function setUp()
    {
        $this->hasMany('Model_FieldTranslation as Translation', array(
            'local' => 'id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        $this->actAs('I18n', array(
        	'className' => 'Model_FieldTranslation',
            'fields' => array('name', 'top_str', 'error_message', 'info_message', 'default_val'),
        	'type' => 'string',
        	'length' => 20,
        	array('i18nField' => 'lang_id'),
        ));
    }
    //======================================================================================================
    
};

class Model_Base_FieldTranslation extends Doctrine_Record {
	
	public function setTableDefinition()
    {
        $this->setTableName('fields_lang');
        
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '8',
        ));
        
        
        $this->hasColumn('lang_id', 'string', 20);
        
        $this->hasColumn('name', 'string', 64);
        $this->hasColumn('top_str', 'string', 64);
        $this->hasColumn('error_message', 'clob');
        $this->hasColumn('info_message', 'clob');
        $this->hasColumn('default_val', 'clob');
        
    }
	//======================================================================================================
    
    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_Field as Field', array(
            'local' => 'id',
            'foreign' => 'id',
        	'owningSide'  =>  true,
        ));
    }
    //======================================================================================================
};
