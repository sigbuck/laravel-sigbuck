<?php

namespace Sigbuck\LaravelSignalbucket\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * @property ?string $signal_url
 * @property ?array $meta_data
 */
class SignalBucketer
{

    private ?string $signal_url = null;
    private ?array $meta_data = null;
    private Client $client;
    private SignalEntity $entity;

    public function __construct(){
        $this->client = new Client([
            'base_uri' => 'https://app.signalbucket.com/api/i/',
        ]);
    }

    private function do_send() {

        $opts = [
            "headers" => [
                "Accept" => "application/json",
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . config("signalbucket.auth_token", env('SIGNAL_BUCKET_KEY'))
            ],
            "json" => [
                "notifications" => json_decode(Cache::get('sigbuck_qn'), true)
            ]
        ];

        try {
            return $this->client->postAsync('signal', $opts)->then(function ($response) {
                $resp = json_decode($response->getBody(), true);
                $successful = isset($resp['success']) && $resp['success'];

                $this->debugLog("processing done");
                if($successful){
                    Cache::forget('sigbuck_qn');
                }

                return [
                    "success" => $successful
                ];
            })->wait();
        }catch (ConnectException $ex) {
            Log::error($ex);
            $this->debugLog("Could not connect to SignalBucket. Please check network");
            return [
                "success" => false,
                "error_code" => $ex->getCode(),
                "error_message" => "Could not connect to SignalBucket. Please check network"
            ];
        }catch (\Exception $ex){
            if($ex->getCode()===401){
				Log::error("Could not authenticate SignalBucket project. Please check that valid project key is passed in .env");
            }else if($ex->getCode()===422){

            }else{
				Log::error($ex);
			}

            return [
                "success" => false,
                "error_code" => $ex->getCode(),
                "error_message" => "Could not connect to SignalBucket. Please check network"
            ];
        }

    }

    public function send(): void
    {

        $this->entity->setURL( $this->signal_url );
        $this->entity->setMetadata( $this->meta_data );

        if(empty($notification->title)){ unset($this->entity->title); }
        if(empty($notification->url)){ unset($this->entity->url); }

        try {
            $cached_signals = json_decode(Cache::get('sigbuck_qn'));
            $sigs = is_array($cached_signals) ? array_slice($cached_signals, 0, 25) : [];
            $sigs[] = $this->entity;
            Cache::put('sigbuck_qn', json_encode($sigs));
            $this->debugLog(count($sigs) . " notifications processing...");
            //TODO: Implement limit on number
            $resp = $this->do_send();
        }catch (Exception $ex){
            Log::error( $ex );
        }

    }
    private function queue_signal(SignalEntity $entity) {
        // TODO: Nicer conversion
        $this->entity = $entity;
    }

    public function success(string $message, string $title=""): SignalBucketer
    {
        $this->debugLog("Queueing INFO notification");
        $s = new SignalEntity("success", $message, $title);
        $this->entity = $s;
        return $this;
    }
    public function info(string $message, string $title=""): SignalBucketer
    {
        $this->debugLog("Queueing INFO notification");
        $s = new SignalEntity("info", $message, $title);
        $this->entity = $s;
        return $this;
    }
    public function broadcast(string $message, string $title=""): SignalBucketer
    {
        $this->debugLog("Queueing BROADCAST notification");
        $s = new SignalEntity("broadcast", $message, $title);
        $this->entity = $s;
        return $this;
    }
    public function warning(string $message, string $title=""): SignalBucketer
    {
        $this->debugLog("Queueing WARNING notification");
        $s = new SignalEntity("warning", $message, $title);
        $this->entity = $s;
        return $this;
    }
    public function critical(string $message, string $title=""): SignalBucketer
    {
        $this->debugLog("Queueing CRITICAL notification");
        $s = new SignalEntity("critical", $message, $title);
        $this->entity = $s;
        return $this;
    }
    public function withURL(string $url, string $btn_text): SignalBucketer
    {
        $this->debugLog("Calling withURL with $url");
        $this->signal_url = "$btn_text|$url";
        return $this;
    }
    public function withData(array $arr): SignalBucketer
    {
        $this->debugLog("setting metadata - " . json_encode($arr));
        $this->meta_data = $arr;
        return $this;
    }
    private function debugLog($str): void
    {
        Log::debug("SignalBucket: $str");
    }
}
