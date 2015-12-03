<?php

use yii\db\Schema;
use yii\db\Migration;

class m151130_081732_create_uploads_table extends Migration
{
  
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
		try{
			
			$this->createTable('uploads_collection', [
				'id' => 'pk',
				'entity_id' => 'INT(11) NOT NULL',
			]);
			
			$this->createTable('uploads', [
				'id' => 'pk',
				'filepath' => 'VARCHAR(255) NOT NULL',
				'collection_id' => 'INT(11) NOT NULL',
			]);
			
			$this->addForeignKey('uploads_collection_uploads_FK', 'uploads', 'collection_id', 'uploads_collection', 'id', 'CASCADE', 'RESTRICT');
			
			
		}catch(\Exception $e){
			return false;
		}
    }

    public function safeDown()
    {
		
		try{
			$this->dropIndex('uploads_collection_uploads_FK', 'uploads');
			$this->dropTable('uploads');
			$this->dropTable('uploads_collection');
		}catch(\Exception $e){
			return false;
		}
    }
   
	
}
