<?php
namespace Yogastama\Tripay\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Facade;


class Tripay {
    public $apiKey;
    public $privateApiKey;
    public $merchantCode;
    public $env;
    public function __construct()
    {
        $this->apiKey = env('API_KEY_TRIPAY');
        $this->privateApiKey = env('API_KEY_PRIVATE_TRIPAY');
        $this->merchantCode = env('TRIPAY_MERCHANT_CODE');
        $this->env = env('APP_ENV');
    }
    public function generateSignature($params){
        $privateKey   = $this->privateApiKey;
        $merchantCode = $this->merchantCode;
        $merchantRef  = $params['merchant_ref'];
        $amount       = $params['amount'];

        $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
        return $signature;
    }
    public function getInstruksiPembayaran($params){
        $url = "https://tripay.co.id/api-sandbox/payment/instruction";
        if ($this->env != 'local') {
            $url = 'https://tripay.co.id/api/payment/instruction';
        }
        $http = Http::withToken($this->apiKey)->get($url, [
            'code' => $params['code'],
            'pay_code' => $params['pay_code'] ?? '',
            'amount' => $params['amount'],
            'allow_html' => $params['allow_html']
        ]);
        return $http->body()['data'];
    }
    public function requestTransaksi($params){
        $url = 'https://tripay.co.id/api-sandbox/transaction/create';
        if($this->env != 'local'){
            $url = 'https://tripay.co.id/api/transaction/create';
        }
        $http = Http::withToken($this->apiKey)
                    ->post($url,
                [
                    'method' => $params['method'],
                    'merchant_ref' => $params['merchant_ref'],
                    'amount' => $params['amount'],
                    'customer_name' => $params['customer_name'],
                    'customer_email' => $params['customer_email'],
                    'customer_phone' => $params['customer_phone'],
                    'callback_url' => $params['callback_url'] ?? '',
                    'return_url' => $params['return_url'],
                    'expired_time' => $params['expired_time'] ?? '',
                    'signature' => $params['signature'],
                    'order_items' => $params['order_items']
                ]);
        return json_decode($http->body());
    }
    public function getDetailTransaksi($params){
        $url = "https://tripay.co.id/api-sandbox/transaction/detail";
        if($this->env != 'local'){
            $url = "https://tripay.co.id/api/transaction/detail";
        }
        $http = Http::withToken($this->apiKey)->get($url, [
            'reference' => $params['reference'],
        ]);
        return json_decode($http->body());
    }
    public function getChannelPembayaran(){
        $url = "https://tripay.co.id/api-sandbox/merchant/payment-channel";
        if($this->env != 'local'){
            $url = "https://tripay.co.id/api/merchant/payment-channel";
        }
        $http = Http::withToken($this->apiKey)->get($url);
        return json_decode($http->body())->data;
    }
    public function getTransaksi($page){
        $url = "https://tripay.co.id/api-sandbox/merchant/transactions";
        if($this->env != 'local'){
            $url = "https://tripay.co.id/api/merchant/transactions";
        }
        $http = Http::withToken($this->apiKey)->get($url, [
            'page' => $page,
            'per_page' => 20,
            'sort' => 'desc'
        ]);

        return json_decode($http->body());
    }
}