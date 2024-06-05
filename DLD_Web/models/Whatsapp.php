<?php

class Whatsapp
{

    private $apiUrl = "https://evo-api.drogarialitoranea.intra.net/message/sendText/entregador-litoranea";
    private $apiKey = "o8b7kce0hw888odig5hg63";

    public function __construct()
    {
    }

    public function sendWhatsappMessage(string $phoneNumber, string $message)
    {
        $headers = array(
            "apikey: $this->apiKey",
            "Content-Type: application/json"
        );

        $payload = array(
            "number" => "55" . $phoneNumber,
            "options" => array(
                "delay" => 1200,
                "presence" => "composing"
            ),
            "textMessage" => array(
                "text" => $message
            )
        );

        // init cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // creating multi-handle cURL
        $mh = curl_multi_init();
        curl_multi_add_handle($mh, $ch);

        // Executing a non-blocking request
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                // Wait for activit from any descriptors
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);

        // Removing and closing handles
        curl_multi_remove_handle($mh, $ch);
        curl_multi_close($mh);

        return true;
    }

    public function sendMultipleWhatsAppMessage(array $numberList, string $message)
    {
        // creating multi-handle cURL
        $mh = curl_multi_init();
        $curlHandles = [];

        foreach ($numberList as $number) {
            $phoneNumber = $number;

            $headers = array(
                "apikey: $this->apiKey",
                "Content-Type: application/json"
            );

            $payload = array(
                "number" => "55" . $phoneNumber,
                "options" => array(
                    "delay" => 1200,
                    "presence" => "composing"
                ),
                "textMessage" => array(
                    "text" => $message
                )
            );

            // init cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // adding handle to multi-handle
            curl_multi_add_handle($mh, $ch);
            $curlHandles[] = $ch;
        }

        // Executing a non-blocking request
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                // Wait for activit from any descriptors
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);

        // Removing and closing handles
        foreach ($curlHandles as $ch) {
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        // closing the multi-handle
        curl_multi_close($mh);

        return true;
    }
}
