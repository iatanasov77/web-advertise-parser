<?php

/**
 * Model_User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */

class Model_User extends Model_Base_User
{

		const WRONG_PW = 0;
		const NOT_FOUND = 1;
	
		/**
		 * 
		 * @param string $username
		 * @param string $password
		 */
		public static function authenticate($username, $password)
		{
			
			$user = Doctrine_Core::getTable('Model_User')->findOneByUsername($username);
			if($user)
			{
				
				if($user->password == md5($password))
					return $user;
				
				//die($user->password);
				//die(md5($password));
				
				throw new Exception(self::WRONG_PW);
			}
			
			throw new Exception(self::NOT_FOUND);
		}
};
