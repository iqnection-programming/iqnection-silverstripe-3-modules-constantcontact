<?php
use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class ConstantContactV2
{
    // Enter your Constant Contact APIKEY and ACCESS_TOKEN
    private static $cckey;
    private static $cctoken;
    
    public function __construct()
    {
        require_once __DIR__.'/../vendor/ConstantContactV2/Ctct/autoload.php';
        $siteConfig = SiteConfig::current_site_config();
        self::$cckey = $siteConfig->CCKey;
        self::$cctoken = $siteConfig->CCToken;
    }
    
    public function addContact($data, $list_id=false)
    {
        $errors = array();
        
        $cc = new ConstantContact(ConstantContactV2::$cckey);
        
        // attempt to fetch lists in the account, catching any exceptions
        try {
            $lists = $cc->getLists(ConstantContactV2::$cctoken);
        } catch (CtctException $ex) {
            foreach ($ex->getErrors() as $error) {
                $errors[] = $error;
            }
        }
        
        // get list from the params, or just the first ACTIVE list if no param was passed
        $the_list = false;
        foreach ($lists as $list) {
            if (!$the_list && $list->status == 'ACTIVE') {
                $the_list = $list;
            }
            if ($list_id == $list->id) {
                $the_list = $list;
                break;
            }
        }
        
        try {
            if ($the_list) {
                $response = $cc->getContactByEmail(ConstantContactV2::$cctoken, $data['email']);
                
                //If the contact doesn't exist, add.  If it does, update.
                if (empty($response->results)) {
                    $contact = new Contact();
                    $contact->addEmail($data['email']);
                    $contact->addList($the_list->id);
                    $contact->first_name = $data['first_name'];
                    $contact->last_name = $data['last_name'];
                    
            
                    /*
                     * The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
                     * Contact that this action is being performed by the contact themselves, and gives the ability to
                     * opt contacts back in and trigger Welcome/Change-of-interest emails.
                     *
                     * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
                     */
                    $returnContact = $cc->addContact(ConstantContactV2::$cctoken, $contact, false);
                    return true;
                } else {
                    $contact = $response->results[0];
                    $contact->addList($the_list->id);
                    $contact->first_name = $data['first_name'];
                    $contact->last_name = $data['last_name'];
        
                    /*
                     * The third parameter of updateContact defaults to false, but if this were set to true it would tell
                     * Constant Contact that this action is being performed by the contact themselves, and gives the ability to
                     * opt contacts back in and trigger Welcome/Change-of-interest emails.
                     *
                     * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
                     */
                    $returnContact = $cc->updateContact(ConstantContactV2::$cctoken, $contact, false);
                    return true;
                }
            }
        } catch (CtctException $ex) {
            /*echo '<pre>';
            print_r($ex->getErrors());
            echo '</pre>';
            die();*/
            $errors[] = $ex->getErrors();
        }
        
        return $errors;
    }
    
    public function getLists()
    {
        $errors = array();
        
        $cc = new ConstantContact(ConstantContactV2::$cckey);
        
        // attempt to fetch lists in the account, catching any exceptions
        try {
            $lists = $cc->getLists(ConstantContactV2::$cctoken);
        } catch (CtctException $ex) {
            foreach ($ex->getErrors() as $error) {
                $errors[] = $error;
            }
            return $errors;
        }
        
        return $lists;
    }
}
