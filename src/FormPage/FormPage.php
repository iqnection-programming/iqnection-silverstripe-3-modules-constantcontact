<?php

namespace IQnection\ConstantContact\FormPage;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms;

class FormPage extends DataExtension
{
	private static $db = [
		'ConstantContactListID' => 'Int'
	];
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$CC = new \IQnection\ConstantContact\ConstantContact();
		$lists = $CC->getLists();
		$listOptions = [];
		foreach($lists as $list)
		{
			$listOptions[$list->id] = $list->name;
		}
		$fields->addFieldToTab('Root.ConstantContact', Forms\DropdownField::create('ConstantContactListID','Add Submissions to List:')
			->setSource($listOptions)
			->setEmptyString('-- Select --') );
	}
}