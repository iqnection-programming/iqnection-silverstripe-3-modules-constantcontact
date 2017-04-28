<?php

    class SiteConfig_ConstantContact extends DataExtension
    {
    
        private static $db = array(
            'CCKey' => 'Varchar(255)',
            'CCToken' => 'Varchar(255)'
        );
                
        public function updateCMSFields(FieldList $fields)
        {
            $tab = $fields->findOrMakeTab('Root.Developer.ConstantContact');
			$tab->push( new TextField('CCKey', 'Constant Contact API Key'));
			$tab->push( new TextField('CCToken', 'Constant Contact API Token'));
			if ($Lists = $this->getCCLists())
			{
				$tab->push( $Lists );
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
