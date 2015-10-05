YOU MUST HAVE CLIENT'S CONSTANT CONTACT LOGINS (LOGGING IN AS OUR MASTER IQ ACCOUNT DOESN'T WORK YOU EXTRA SILLY GOOSE)

Log in to Constant Contact as the client: https://login.constantcontact.com/login

Log in to Mashery (https://constantcontact.mashery.com/login/)

Go to the API Keys tab, enter the Key in the box and click "Get Token".

Plug your token into the Site Settings and save.

After saved, you should get a list of lists from Constant Contact

Make a thing that submits forms, add this guy:
	$cc = new ConstantContactV2();
	$cc->addContact(array(
		"first_name" => "xxx",
		"last_name" => "xxx",
		"email" => "xxx@xxx.xxx"
	),$listID);