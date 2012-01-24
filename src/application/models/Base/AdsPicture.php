<?php

class Model_Base_AdsPicture extends Doctrine_Record {
	
	public function setTableDefinition()
    {
        $this->setTableName('ads_pictures');
        
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('ad_id', 'integer', 11);
        
        $this->hasColumn('picture', 'string', 64);
        $this->hasColumn('order_no', 'integer', 2);
    }
    //======================================================================================================
    
    public function setUp()
    {
        $this->hasOne('Model_Ads as ads', array(
            'local' => 'ad_id',
            'foreign' => 'id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
    }
    //======================================================================================================

};
    