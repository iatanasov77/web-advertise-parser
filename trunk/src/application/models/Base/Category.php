<?php
/**
 * SELECT c.id , cl.name  FROM `categories` as c LEFT JOIN (`categories_lang` as cl) ON (c.id=cl.id AND cl.lang_id='bulgarian')
 * 
 * @author vanko
 *
 */

// Connection Component Binding
//Doctrine_Manager::getInstance()->bindComponent('Model_Category', 'doctrine');

abstract class Model_Base_Category extends Doctrine_Record
{
	
	/**

	 */
	public function __get($varName) {
		if($varName == 'name') {
			die(var_dump($this->Translation));	
		}
		if(is_array($this->Translation)) {
			if(array_key_exists($varName, $this->Translation[0])){
				return $this->Translation[0][$varName];
			}
			
			return NULL;
		}
	}
	
    public function setTableDefinition()
    {
        $this->setTableName('categories');
        
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('parent_id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'notnull'  => true,
        ));
        
        $this->hasColumn('fieldset', 'integer', 2);
       
    }
	//======================================================================================================
    
    public function setUp()
    {
        $this->hasOne('Model_Fieldset as Fieldset', array(
            'local' => 'fieldset',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT'
        ));
        
        $this->hasOne('Model_Category as Parent', array(
                'local'    => 'id',
                'foreign'  => 'parent_id'
            )
        );

        $this->hasMany('Model_Category as Children', array(
                'local'    => 'id',
                'foreign'  => 'parent_id'
            )
        );
        
        $this->hasMany('Model_CategoryTranslation as Translation', array(
            'local' => 'id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        $this->actAs('I18n', array(
        	'className' => 'Model_CategoryTranslation',
            'fields' => array('name', 'description', 'page_title', 'meta_keywords', 'meta_description'),
        	'type' => 'string',
        	'length' => 20,
        	array('i18nField' => 'lang_id'),
        ));
    }
    //======================================================================================================
    
};


abstract class Model_Base_CategoryTranslation extends Doctrine_Record {
	
    public function setTableDefinition()
    {
        $this->setTableName('categories_lang');
        
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '8',
        ));
        
        
        $this->hasColumn('lang_id', 'string', 20);
        
        $this->hasColumn('name', 'string', 128);
        $this->hasColumn('description', 'clob');
        $this->hasColumn('page_title', 'string', 250);
        $this->hasColumn('meta_keywords', 'clob');
        $this->hasColumn('meta_description', 'clob');
        
    }
	//======================================================================================================
    
    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_Category as Category', array(
            'local' => 'id',
            'foreign' => 'id',
        	'owningSide'  =>  true,
        ));
    }
    //======================================================================================================
    
};
