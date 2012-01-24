<?php

class Model_Base_ParserProject extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('parser_projects');
        
        $this->hasColumn('id', 'integer', 4, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('url', 'string', 128, array('unique' => true));
        
        /*
         * Foreign Keys
         */
        $this->hasColumn('category_id', 'integer', 11);
        $this->hasColumn('user_id', 'integer', 11);
        
        $this->hasColumn('project_title', 'string', 128);
        $this->hasColumn('nopic', 'string', 128);
        
        $this->hasColumn('active', 'integer', 1);
        
        /* Indexes */
        $this->index('url', array(
                'fields' => array('url'),
                'type' => 'unique',
            )
        );
        
        
        
	}
	
	public function setUp()
	{
		$this->hasOne('Model_User as user', array(
            'local' => 'user_id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
		$this->hasOne('Model_Category as category', array(
            'local' => 'category_id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
		
		$this->hasMany('Model_ParserProjectField as fields', array(
            'local' => 'id',
            'foreign' => 'projects_id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        $this->hasMany('Model_ParserProjectFieldAds as fieldsAds', array(
            'local' => 'id',
            'foreign' => 'projects_id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        $this->hasMany('Model_ParserProjectFieldAdsPicture as fieldsAdsPictures', array(
            'local' => 'id',
            'foreign' => 'projects_id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
	}
	
	public function preSave($event)
    {   
	/*
        $q = Doctrine_Query::create()
    		->delete('Model_ParserProject pp')
    		->where('pp.url = ?')
		->orWhere('pp.id = ?');
		$q->execute(array($this->url, $this->id));
	*/
    }
	
};
