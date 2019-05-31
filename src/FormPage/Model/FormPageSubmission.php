<?php

namespace IQnection\ConstantContact\FormPage\Model;

use IQnection\ConstantContact\ConstantContact;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms;

class FormPageSubmission extends DataExtension
{
	private static $db = [
		'ConstantContactData' => 'Text'
	];
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$fields->removeByName('ConstantContactData');
		$fields->findOrMakeTab('Root.Developer.ConstantContact');
		$fields->addFieldToTab('Root.Developer.ConstantContact', Forms\LiteralField::create('ccdata','<div style="max-width:100%;overflow:auto;"><pre><xmp>'.print_r(json_decode($this->owner->ConstantContactData,1),1).'</xmp></pre></div>') );
	}
	
	public function onBeforeWrite()
	{
		if (!$this->owner->Exists())
		{
			$this->addToConstantContact();
		}
	}
	
	public function addToConstantContact()
	{
		if ($list_id = $this->owner->Page()->ConstantContactListID)
		{
			$cc = new ConstantContact();
			$this->owner->extend('onBeforeConstantContactAdd',$cc);
			$result = $cc->addContact($this->owner->Email, $list_id, $this->owner->FirstName, $this->owner->LastName);
			$this->owner->ConstantContactData = json_encode($result);
			$this->owner->extend('onAfterConstantContactAdd',$result);
		}
		return $this;
	}
}