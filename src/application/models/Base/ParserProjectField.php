<?php


class Model_Base_ParserProjectField extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('parser_projects_fields');
        
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('projects_id', 'integer', 4);
        
        $this->hasColumn('fields_caption', 'string', 200);
        $this->hasColumn('xquery', 'clob');
        
        /* Indexes */
        /*
        $this->index('projectField', array(
                'fields' => array('projects_id', 'fields_caption'),
                'type' => 'unique',
            )
        );
        */
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
                'fields'    => array('projects_id', 'fields_caption'),
                'canUpdate' => true
            )
        );
        
	}
	
	public function preSave($event)
    {
        $q = Doctrine_Query::create()
    		->delete('Model_ParserProjectField ppf')
    		->where('ppf.projects_id = ?')
    		->andWhere('ppf.fields_caption = ?');
		$q->execute(array($this->projects_id, $this->fields_caption));
    }
	
};
