<?php

namespace IQnection\ConstantContact;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStrpie\Forms;
use SilverStripe\SiteConfig\SiteConfig;
use Ctct\ConstantContact as CC;
use Ctct\Components\Contacts\Contact;
use Ctct\Exceptions\CtctException;

class ConstantContact
{
    // Enter your Constant Contact APIKEY and ACCESS_TOKEN
    protected $ApiKey;
    protected $ApiToken;
    
    public function __construct($ApiKey=null,$ApiToken=null)
    {
        $siteConfig = SiteConfig::current_site_config();
        $this->setApiKey( ($ApiKey) ? $ApiKey : $siteConfig->ConstantContactApiKey );
        $this->setApiToken( ($ApiToken) ? $ApiToken : $siteConfig->ConstantContactApiToken );
    }
	
	public function setApiKey($key)
	{
		$this->ApiKey = $key;
	}
	
	public function setApiToken($token)
	{
		$this->ApiToken = $token;
	}
    
    public function addContact($Email, $list_id=false, $FirstName = null, $LastName = null)
    {
        $errors = array();
        
        $cc = new CC($this->ApiKey);
        // attempt to fetch lists in the account, catching any exceptions
        try {
            $lists = $cc->listService->getLists($this->ApiToken);
        } catch (CtctException $ex) {
            foreach ($ex->getErrors() as $error) 
			{
                $errors[] = $error;
            }
        }
        
        // get list from the params, or just the first ACTIVE list if no param was passed
        $the_list = false;
        foreach ($lists as $list) 
		{
            if (!$the_list && $list->status == 'ACTIVE') 
			{
                $the_list = $list;
            }
            if ($list_id == $list->id) 
			{
                $the_list = $list;
                break;
            }
        }
        
		if ($the_list) 
		{
			try {
                $response = $cc->contactService->getContacts($this->ApiToken, array('email' => $Email));
                
                //If the contact doesn't exist, add.  If it does, update.
                if (empty($response->results)) 
				{
                    $contact = new Contact();
                    $contact->addEmail($Email);
                    $contact->addList($the_list->id);
                    $contact->first_name = $FirstName;
                    $contact->last_name = $LastName;
                    
            
                    /*
                     * The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
                     * Contact that this action is being performed by the contact themselves, and gives the ability to
                     * opt contacts back in and trigger Welcome/Change-of-interest emails.
                     *
                     * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
                     */
                    $returnContact = $cc->contactService->addContact($this->ApiToken, $contact);
                    return $returnContact;
                } 
				else 
				{
					// update contact
                    $contact = $response->results[0];
					if ($contact instanceof Contact)
					{
						$contact->addList($the_list->id);
						if ($FirstName) { $contact->first_name = $FirstName; }
						if ($LastName) { $contact->last_name = $LastName; }
			
						/*
						 * The third parameter of updateContact defaults to false, but if this were set to true it would tell
						 * Constant Contact that this action is being performed by the contact themselves, and gives the ability to
						 * opt contacts back in and trigger Welcome/Change-of-interest emails.
						 *
						 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
						 */
						$returnContact = $cc->contactService->updateContact($this->ApiToken, $contact);
						return $returnContact;
					}
                }
			} catch (CtctException $ex) {
				$errors[] = $ex->getErrors();
			}
		}
        
        return $errors;
    }
    
    public function getLists()
    {
        $errors = array();
        
        $cc = new CC($this->ApiKey);

        // attempt to fetch lists in the account, catching any exceptions
        try {
            $lists = $cc->listService->getLists($this->ApiToken);
        } catch (CtctException $ex) {
            foreach ($ex->getErrors() as $error) {
                $errors[] = $error;
            }
            return $errors;
        }
        
        return $lists;
    }
}
