<?php


namespace MVC;


class JWT
{

    private $header = [
      'alg' => 'sha256',
      'typ' => 'JWT'
    ];

    private $secret = SECRET;

    public function __construct(){}

    public function encode(array $data): string
    {
        $data = ['created' => time(), 'data' => $data];
        $h = base64_encode(json_encode($this->header));
        $b = base64_encode(json_encode($data));

        $s = base64_encode(
            hash($this->header['alg'], json_encode($this->header) . json_encode($data) . $this->secret)
        );

        return $this->concat($h, $b, $s);
    }

    public function getData(string $token): array
    {
        $arr = explode(".", $token);

        //$headers = json_decode(base64_decode($arr[0]), true);
        $data = json_decode(base64_decode($arr[1]), true);
        //$signature = base64_decode($arr[2]);

        return $data['data'];
    }

    public function verify(string $token): bool
    {
        $arr = explode(".", $token);
        $data = json_decode(base64_decode($arr[1]), true);
        $signature = base64_decode($arr[2]);

        $s = hash($this->header['alg'], json_encode($this->header) . json_encode($data) . $this->secret);

        return $signature === $s;
    }


    private function concat(string...$args): string
    {
        $result = "";
        foreach ($args as $arg){
            $result .= $arg . ".";
        }

        return trim($result, ".");
    }

}