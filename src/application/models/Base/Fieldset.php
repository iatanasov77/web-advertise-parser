<?php


Doctrine_Manager::getInstance()->bindComponent('Model_Fieldset', 'doctrine');

class Model_Base_Fieldset extends Doctrine_Record {
	
	
	public function setTableDefinition()
    {
        $this->setTableName('fieldsets');
        
        $this->hasColumn('id', 'integer', 5, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('name', 'string', 64);
        $this->hasColumn('description', 'clob');
         
    }
	//======================================================================================================
    
    public function setUp()
    {
        $this->hasMany('Model_Field as Fields', array(
            'local' => 'id',
            'foreign' => 'fieldset',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
        ));
        
        $this->hasMany('Model_Category as Categories', array(
            'local' => 'id',
            'foreign' => 'fieldset',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
        ));
    }
    //======================================================================================================
    
};
