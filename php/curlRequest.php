<?php

class CurlRequest
{
    private $private_key = '';
    private $token = '';
    private $curl_handle;

    public function __construct($private_key, $token)
    {
        $this->private_key = $private_key;
        $this->token = $token;
        $this->curl_handle = null;
    }

    public function __destruct()
    {
        if ($this->curl_handle !== null) {
            curl_close($this->curl_handle);
        }
    }

    /**
     * Executes a cURL request to the CoinPayments.net API.
     *
     * @param string $command The API command to call.
     * @param array $fields The required and optional fields to pass with the command.
     *
     * @return array Result data with error message. Error will equal "ok" if call was a success.
     *
     * @throws Exception If an invalid format is passed.
     */
    public function execute($command, array $fields = [])
    {
        // Define the API url
        $api_url = 'https://globalpay.network/api/v1/public-api/' . $command;

        // Set default field values
        $request = ["data" => $fields];
        $request['_version'] = 1;
        $request['_channel'] = "rest";
        $request['_sdk'] = "php";

        // Generate query string from fields
        $post_fields = json_encode($request);
        $data_fields = json_encode($fields, JSON_UNESCAPED_SLASHES , 512);

        // Generate the HMAC hash from the query string and private key
        $hmac = hash_hmac('sha512', $data_fields, $this->private_key);

        // Check the cURL handle has not already been initiated
        if ($this->curl_handle === null) {

            // Initiate the cURL handle and set initial options
            $this->curl_handle = curl_init($api_url);
            curl_setopt($this->curl_handle, CURLOPT_FAILONERROR, TRUE);
            curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->curl_handle, CURLOPT_POST, TRUE);
        }

        // Set HMAC header for cURL
        curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, array("Authorization: token $this->token\n" . "Content-Type:application/json\n" . 'HMAC:' . $hmac));

        //curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, array(''));

        // Set HTTP POST fields for cURL
        curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $post_fields);

        // Execute the cURL session
        $response = curl_exec($this->curl_handle);

        // Check the response of the cURL session
        if ($response !== FALSE) {
            $result = false;

            // Prepare json result
            if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // We are on 32-bit PHP, so use the bigint as string option.
                // If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
                $decoded = json_decode($response, TRUE, 512, JSON_BIGINT_AS_STRING);
            } else {
                $decoded = json_decode($response, TRUE);
            }

            // Check the json decoding and set an error in the result if it failed
            if ($decoded !== NULL && count($decoded)) {
                $result = $decoded;
            } else {
                $result = ['error' => 'Unable to parse JSON result (' . json_last_error() . ')'];
            }
        } else {
            // Throw an error if the response of the cURL session is false
            $result = ['error' => 'cURL error: ' . curl_error($this->curl_handle)];
        }

        return $result;
    }
}
