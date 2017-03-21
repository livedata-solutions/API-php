<?php

class LiveDataAPI 
{

	public $api_user = '';
    public $api_pass = '';
    public $api_id = null;
    public $api_url = 'http://www1.livedata-emailing.com';

	private $fullurl;

	public function __construct($api_user=null, $api_pass=null, $api_id=null, $api_url=null) 
	{
		if ($api_user != null) {
			$this->api_user = $api_user;
		}
		if ($api_pass != null) {
			$this->api_pass = $api_pass;
		}
		if ($api_id != null) {
			$this->api_id = $api_id;
		}
		if ($api_url != null) {
			$this->api_url = $api_url;
		}
		$this->fullurl = $this->joinPaths($this->api_url, 'sliprestful/bases', $this->api_id);
	}



	/**
	 * 3.1.6 Add / Edit contact
	 *
	*/

	public function Add_Edit_Contact($email, $attributes) 
	{
		return $this->callLivedataAPI($this->joinPaths('contacts', $email), 'PUT', $attributes);
	}

	/**
     * 3.1.8 Delete contact from database
     *
    */

	public function Delete_Contact($email)
	{
		return $this->callLivedataAPI($this->joinPaths('contacts', $email), 'DELETE');
	}

    /**
     * 3.1.9 Unsubscribe contact
     * 
    */ 
       
    public function Unsubscribe_Contact($email)
    {  
        return $this->callLivedataAPI($this->joinPaths('contacts', $email, 'unsubscription'), 'PUT');
    }

	/**
     * 3.2 Get Unsubscribes
     *
    */

	public function Get_Unsubscribes($page=null, $fromDate=null, $toDate=null)
	{
		$filters = [];
		if ($page != null) {
			$filters['page'] = $page;
		}
		if ($fromDate != null) {
			$filters['fromDate'] = $fromDate;
		}
		if ($toDate != null) {
			$filters['toDate'] = $toDate;
		}
		if (empty($filters)) {
			return $this->callLivedataAPI('unsubscribes');
		} 
		else {
			return $this->callLivedataAPI('unsubscribes?' . http_build_query($filters));
		}
	}



	public function checkAPI()
	{  
        return !(empty($this->api_url) || empty($this->api_id) || empty($this->api_user) ||
               empty($this->api_pass) || !filter_var($this->api_url, FILTER_VALIDATE_URL));
    }
	

    private function callLivedataAPI($action, $request = null, $payload = null)
    {
		if ($this->checkAPI()) {
            $curl = curl_init();
			$url = $this->joinPaths($this->fullurl, $action);
			$headers = $this->generateWSSEHeader();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($curl, CURLOPT_TIMEOUT, 6);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
            if (!is_null($request)) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
			} else {
				$request = 'GET';
			}
            if (!is_null($payload)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
            }

			echo "\n" . $request . " " . $url . "\n";
            // make the call
			$result = curl_exec($curl);

			if (curl_error($curl)) 
			{
				$result = [
					'statusCode' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
					'status' => 'error',
					'message' => curl_error($curl)
				];
				return json_encode($result);
			}

			// close the curl session
            curl_close($curl);

            //We assure that we get a good curl response
			return $result;
        }

		//Catch all errors and return empty array
		$result = [
			'statusCode' => 400,
			'status' => 'error',
			'message' => 'API parameters not configured' 
		];
        return json_encode($result);
    }

	

	/**
	 * Function for generate WSSE header for connect to API
	*/
	private function generateWSSEHeader() 
	{
		$created = date('c');
		$nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
	    $nonce64 = base64_encode($nonce);
		$passwordDigest = base64_encode(sha1($nonce . $created . $this->api_pass, true));
		return array(sprintf(
			'X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
			$this->api_user,
			$passwordDigest,
			$nonce64,
			$created
		));

	}


    /**
      Function that emulates os.join in Python
      It allows to create url paths without trouble with slashes
      http://stackoverflow.com/a/15575293/276186
    */
    private function joinPaths()
    {
        $paths = array();
        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }
        $tmp = preg_replace('#/+#', '/', implode('/', $paths));
        // Aditional check to solve double slash if it begins with http or https
        return preg_replace('/(^https?:\/)([a-zA-Z0-9])/', '${1}/${2}', $tmp);
    }


}
