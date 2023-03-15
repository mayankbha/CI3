<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cron extends CI_Controller 
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function completeMoreTreesApiRequest()
    {            
        $unfulfilledTransactions = $this->base_model->fetch_records_from('book_purchases', array('api_val > '=>'0','moretrees_success'=>'0'));
        $resultArr = [];
        foreach($unfulfilledTransactions as $purRec) {
            $message = 'Something went wrong, please try again later';
            $buyer_details = getUserRec($purRec->user_id);

            if ($buyer_details->first_name == '') {
                    $buyer_name = $buyer_details->username;
                }else{
                    $buyer_name = $buyer_details->first_name.' '.$buyer_details->last_name;
                }

            if($purRec->api_val > 0 && $purRec->moretrees_success == '0' && !empty($buyer_details)) {
                $this->load->library('moretreesapi');
                $plantRes = $this->moretreesapi->plantATree([
                    'first_name' => $buyer_name,
                    'email' => $buyer_details->email
                ]);
                if(isset($plantRes['result']) && !empty($plantRes['result']) && isset($plantRes['result']['status']) && $plantRes['result']['status'] == 1  && isset($plantRes['result']['response']) && $plantRes['result']['response'] == 'successful') {
                    $certUrl = (isset($plantRes['result']['data']['certificates']) && count($plantRes['result']['data']['certificates']) > 0) ? $plantRes['result']['data']['certificates'][0]['certificateURL'] : '';
                    $this->base_model->update_operation(array('moretrees_success' => '1', 'moretrees_cert_url' => $certUrl), 'book_purchases', array('purchase_id' => $_GET['pid']));
                    $message = 'record updated successfully';
                } else {
                    $message = (isset($plantRes['result']['errors']) && isset($plantRes['result']['errors'][0])) ? $plantRes['result']['errors'][0]['msg'] : 'Moretrees API returned an error, please try again later';
                }
            }
            $resultArr[] = [
                'purchase_id' => $purRec->purchase_id,
                'message' => $message
            ];
        }
        echo "<pre>";print_r($resultArr);exit;
    }
}
?>