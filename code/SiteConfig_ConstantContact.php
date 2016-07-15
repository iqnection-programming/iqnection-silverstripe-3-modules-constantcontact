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
                $fields->addFieldToTab('Root.ConstantContact', new LiteralField('cclists', '<h2>Lists</h2>'.$this->getCCLists()));
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
            $the_lists = '</ul>';
            foreach ($lists as $list) {
                $the_lists .= '<li>'.$list->id.' : '.$list->name.' ('.$list->contact_count.')</li>';
            }
            $the_lists .= '</ul>';
            return $the_lists;
        }
    }
