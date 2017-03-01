<?php

    class SiteConfig_ConstantContact extends DataExtension
    {
    
        private static $db = array(
            'CCKey' => 'Varchar(255)',
            'CCToken' => 'Varchar(255)'
        );
                
        public function updateCMSFields(FieldList $fields)
        {
            // only admins can modify these fields
            if (Permission::check('ADMIN')) {
                $fields->addFieldToTab('Root.ConstantContact', new TextField('CCKey', 'Constant Contact API Key'));
                $fields->addFieldToTab('Root.ConstantContact', new TextField('CCToken', 'Constant Contact API Token'));
                if ($Lists = $this->getCCLists())
				{
	                $fields->addFieldToTab('Root.ConstantContact', $Lists );//new LiteralField('cclists', '<h2>Lists</h2>'.$this->getCCLists()));
				}				
            }
        }
        
        private function getCCLists()
        {
            if (!$this->owner->CCKey || !$this->owner->CCToken) {
                return null;
            }
            
            $CC = new ConstantContactV2();
            $lists = $CC->getLists();
            
            // get list from the params, or just the first ACTIVE list if no param was passed
            $the_lists = new ArrayList();
            foreach ($lists as $list) 
			{
				$the_lists->push(new DataObject(array(
					'ID' => $list->id,
					'Name' => $list->name,
					'Contacts' => $list->contact_count
				)));
            }
			$gf_config = GridFieldConfig_Base::create();
			$gf_config->getComponentByType('GridFieldDataColumns')->setDisplayFields(array('ID'=>'List ID','Name'=>'Name','Contacts' => 'Contacts'));
            $gridField = GridField::create(
				'CCLists',
				'Constant Contact Lists',
				$the_lists,
				$gf_config
			);
            return $gridField;
        }
    }
