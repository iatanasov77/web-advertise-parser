<?php


class Model_Base_ParserProjectFieldAdsPicture extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('parser_projects_fields_ads_pictures');
        
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('projects_id', 'integer', 4);
        $this->hasColumn('xquery', 'clob');
        $this->hasColumn('regex', 'string', 128);
		$this->hasColumn('replace', 'string', 45);

        
	}
	
	public function setUp()
	{
		$this->hasOne('Model_ParserProject as project', array(
            'local' => 'projects_id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        $this->actAs('Sluggable', array(
                'unique'    => true,
                'fields'    => array('projects_id', 'id'),
                'canUpdate' => true
            )
        );
		
        
	}
	
	public function preSave($event)
	{
		/*
		$q = Doctrine_Query::create()
                	->delete('Model_ParserProjectFieldAdsPicture ppfap')
                	->where('ppfap.projects_id = ?');
                $q->execute(array($this->projects_id));
                
*/
	}
};
