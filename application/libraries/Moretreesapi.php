<?php
class Moretreesapi {
		
		private $publicValidationKey;
        private $baseApiUrl;
        private $publicApiKey;
        private $ci_instance;

		public function __construct()
		{
            $this->ci_instance = & get_instance();
			$this->publicValidationKey = $this->ci_instance->config->item('site_settings')->moretrees_validation_key;
            $this->publicApiKey = $this->ci_instance->config->item('site_settings')->moretrees_api_key;
            $this->baseApiUrl = 'https://api.moretrees.eco/';
		}

        private function createRequest($type, $endpoint, $data) {
            $curl = curl_init();
            $qString = '';
            if($type == 'GET') {
                $qString = '?'.http_build_query($data);
            }
            curl_setopt($curl, CURLOPT_URL, $this->baseApiUrl.$endpoint.$qString);  
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            if($type == 'POST') {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            $headers = [
                'Content-Type: application/json',
                'Authorization: '.$this->publicValidationKey
            ];
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            return [
                'responseInfo' => $info,
                'result' => json_decode($result, true)
            ];
        }

        public function getCreditBalance() {
            $data = [];
            $endpoint = 'v1/basic/viewCredits';
            $response = $this->createRequest('GET', $endpoint, json_encode($data));
            return $response;
        }

        public function plantATree($userData) {
            $data = [
                'type_slug' => 'any_tree',
                'request_type' => 2,
                'users' => [
                    [
                        'first_name' => (!empty($userData['first_name'])) ? $userData['first_name'] : 'Noname',
                        'email' => $userData['email'],
                        'quantity' => 1
                    ]
                ]
            ];
            $endpoint = 'v1/basic/planttree';
            $response = $this->createRequest('POST', $endpoint, json_encode($data));
            return $response;
        }
}