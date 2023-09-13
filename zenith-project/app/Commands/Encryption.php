<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Encryption extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Encryption';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'encryption';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '데이터 암호화/복호화';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'type' => 'Set encrypt/decrypt',
        'text' => 'Encrypt(Decrypt) for Text',
        'key' => 'Encrypt(Decrypt) for Key'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    protected $encrypter;
    protected $key = 'f907464a49e95e8d778cf87374cc59ccd7669f5924e25776320ec69479206a22';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        if(!isset($params[0]))
            throw new \Exception('"encrypt"또는"decrypt"를 지정해주세요.');
        if(!isset($params[1]))
            throw new \Exception('암호화(복호화) 할 문자를 입력해주세요.');

        $type = $params[0];
        $this->encrypter = \Config\Services::encrypter();
        switch($type) {
            case 'encrypt' :
                $result = $this->encrypt($params[1]);
                break;
            case 'decrypt' :
                $result = $this->decrypt($params[1]);
                break;
            case 'sodium_encrypt' :
                $result = $this->sodium_encrypt($params[1]);
                break;
            case 'sodium_decrypt' :
                $result = $this->sodium_decrypt($params[1]);
                break;
            default :
                throw new \RuntimeException('"encrypt"또는"decrypt"를 지정해주세요.');
                break;
        }
        var_dump($result);
    }

    private function encrypt($text) {
        $result = $this->encrypter->encrypt($text);
        return sodium_bin2hex($result);
    }

    private function decrypt($text) {
        $text = sodium_hex2bin($text);
        return $this->encrypter->decrypt($text);
    }

    private function sodium_encrypt($text) {
        // create a nonce for this operation
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); // 24 bytes

        $data = sodium_pad($text, 16);

        // encrypt message and combine with nonce
        $ciphertext = $nonce . sodium_crypto_secretbox($data, $nonce, sodium_hex2bin($this->key));

        // cleanup buffers
        sodium_memzero($data);
        sodium_memzero($this->key);

        return sodium_bin2hex($ciphertext);
    }

    private function sodium_decrypt($text) {
        $text = sodium_hex2bin($text);
        // Extract info from encrypted data
        $nonce      = mb_substr($text, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($text, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        // decrypt data
        $data = sodium_crypto_secretbox_open($ciphertext, $nonce, sodium_hex2bin($this->key));
        $data = sodium_unpad($data,16);

        return $data;
    }
}
