<?php


class Model_Base_Ads extends Doctrine_Record {
	
	public function setTableDefinition()
    {
        $this->setTableName('ads');
        
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'unsigned' => true,
            'primary' => true,
            'notnull'  => true,
            'autoincrement' => true,
        ));
        
        $this->hasColumn('parser_project_id', 'integer', 4);
        $this->hasColumn('parser_project_ads_url', 'clob');
        
        $this->hasColumn('user_id', 'integer', 11);
        $this->hasColumn('category_id', 'integer', 5);
        $this->hasColumn('package_id', 'integer', 5);
        $this->hasColumn('usr_pkg', 'integer', 10);
        
        $this->hasColumn('date_added','datetime');
        $this->hasColumn('date_expires','datetime');
        
        $this->hasColumn('title', 'string', 150);
        $this->hasColumn('description', 'clob');
        
        $this->hasColumn('price', 'float');
        
        $this->hasColumn('currency', 'string', 10);
        $this->hasColumn('country', 'string', 150);
        $this->hasColumn('region', 'string', 150);
        $this->hasColumn('city', 'string', 150);
        $this->hasColumn('zip', 'string', 150);
        
        $this->hasColumn('meta_description', 'clob');
        $this->hasColumn('meta_keywords', 'clob');
        
        $this->hasColumn('sold', 'integer', 1);
        $this->hasColumn('rented', 'integer', 1);
        $this->hasColumn('viewed', 'integer', 10);
        $this->hasColumn('user_approved', 'integer', 1);
        $this->hasColumn('active', 'integer', 1);
        $this->hasColumn('pending', 'integer', 1);
        $this->hasColumn('featured', 'integer', 1);
        $this->hasColumn('highlited', 'integer', 1);
        $this->hasColumn('priority', 'integer', 1);
        
        $this->hasColumn('video', 'clob');
        $this->hasColumn('rating', 'float');
        
        $this->hasColumn('language', 'string', 30);
        $this->hasColumn('make', 'string', 64);
        $this->hasColumn('motorcycle_make', 'string', 64);
        $this->hasColumn('boat_make', 'string', 64);
        $this->hasColumn('rv_make', 'string', 64);
        $this->hasColumn('truck_make', 'string', 64);
        $this->hasColumn('other_make', 'string', 64);
        $this->hasColumn('model', 'string', 64);
        $this->hasColumn('model1', 'string', 64);
        $this->hasColumn('other_model', 'string', 64);
        
        $this->hasColumn('year', 'integer', 1);
        $this->hasColumn('mileage', 'integer', 1);
        
        $this->hasColumn('bodystyle', 'string', 64);
        $this->hasColumn('transmission', 'string', 64);
        $this->hasColumn('fuel', 'string', 64);
        $this->hasColumn('doors', 'string', 128);
        $this->hasColumn('color', 'string', 64);
        $this->hasColumn('condition_vehicles', 'string', 64);
        
        $this->hasColumn('boat_length', 'float');
        $this->hasColumn('boat_type', 'string', 64);
        $this->hasColumn('hull_type', 'string', 64);
        $this->hasColumn('aircraft_type', 'string', 64);
        
        $this->hasColumn('vehicle_options', 'clob');
        $this->hasColumn('motorcycle_features', 'clob');
        $this->hasColumn('rv_features', 'clob');
        
        $this->hasColumn('property_type', 'string', 64);
        $this->hasColumn('bedrooms', 'integer', 2);
        $this->hasColumn('bathrooms', 'integer', 2);
        $this->hasColumn('area', 'float');
        $this->hasColumn('year_built', 'integer', 4);
        
        $this->hasColumn('estate_condition', 'string', 64);
        $this->hasColumn('amenities', 'clob');
        $this->hasColumn('community_amenities', 'clob');
        $this->hasColumn('breed', 'string', 64);
        $this->hasColumn('age', 'integer', 3);
        $this->hasColumn('sex', 'string', 64);
        $this->hasColumn('industry', 'string', 64);
        $this->hasColumn('job_type', 'string', 64);
        
        $this->hasColumn('work_experience', 'string', 64);
        $this->hasColumn('age1', 'integer', 3);
        $this->hasColumn('interested_in', 'string', 64);
        $this->hasColumn('marital_status', 'string', 64);
        $this->hasColumn('ad_type', 'string', 64);
    }
	//======================================================================================================
    
    public function setUp()
    {
        $this->hasMany('Model_AdsPicture as pictures', array(
            'local' => 'id',
            'foreign' => 'ad_id',
            'onUpdate'   => 'CASCADE',
            'onDelete'   => 'RESTRICT',
            'owningSide' => false,
        ));
        
        
    }
    //======================================================================================================
    
};