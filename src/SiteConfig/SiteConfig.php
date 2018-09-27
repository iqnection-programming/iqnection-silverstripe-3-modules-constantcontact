<?php

namespace IQnection\ConstantContact\SiteConfig;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms;

class SiteConfig extends DataExtension
{
	private static $db = array(
		'ConstantContactApiKey' => 'Varchar(255)',
		'ConstantContactApiToken' => 'Varchar(255)'
	);
			
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$tab = $fields->findOrMakeTab('Root.Developer.ConstantContact');
		$fields->addFieldToTab('Root.Developer.ConstantContact', Forms\TextField::create('ConstantContactApiKey', 'API Key') );
		$fields->addFieldToTab('Root.Developer.ConstantContact', Forms\TextField::create('ConstantContactApiToken', 'API Token') );

		if ($Lists = $this->getCCLists())
		{
			$tab->push( $Lists );
		}	

	}
	
	private function getCCLists()
	{
		if (!$this->owner->ConstantContactApiKey || !$this->owner->ConstantContactApiToken) {
			return null;
		}
		
		$CC = new ConstantContact();
		$lists = $CC->getLists();
		
		// get list from the params, or just the first ACTIVE list if no param was passed
		$the_lists = ArrayList::create();
		foreach ($lists as $list) 
		{
			$the_lists->push(ArrayData::create(array(
				'ID' => $list->id,
				'Name' => $list->name,
				'Contacts' => $list->contact_count
			)));
		}
		$gf_config = Forms\GridField\GridFieldConfig_Base::create();
		$gf_config->getComponentByType(Forms\GridField\GridFieldDataColumns::class)->setDisplayFields(array('ID'=>'List ID','Name'=>'Name','Contacts' => 'Contacts'));
		$gridField = Forms\GridField\GridField::create(
			'CCLists',
			'Constant Contact Lists',
			$the_lists,
			$gf_config
		);
		return $gridField;
	}
}
