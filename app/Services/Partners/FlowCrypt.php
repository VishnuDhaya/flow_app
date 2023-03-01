<?php

namespace App\Services\Partners;


class FlowCrypt
{
    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    private $cipher = 'aes-256-gcm';

    /**
     * The password used to generate encryption key.
     *
     * @var string
     */
    private $password;

    /**
     * The size of the cipher algorithm.
     *
     * @var int
     */
    private $cipher_size = 32;

    /**
     * The digest used for hashing.
     *
     * @var string
     */
    private $digest_algo = 'sha256';

    /**
     * The length of encryption key.
     *
     * @var int
     */
    private $key_length = 32;

    /**
     * The number of hashing iterations.
     *
     * @var int
     */
    private $iterations = 40579;

    /**
     * The length of salt used.
     *
     * @var int
     */
    private $salt_length = 16;


    /**
     * The length of tag used.
     *
     * @var int
     */
    private $tag_length = 16;

    /**
     * Construct the object 
     *
     * @param  string  $password
     */
    public function __construct($password)
    {

        if (!$password) {
            thrw('Encryption Password not set');
        }
        $this->password = $password;
    }

    /**
     * Generate the encryption key.
     *
     * @param  string  $salt
     * @return string
     */
    private function generate_key(string $salt)
    {

        $key = openssl_pbkdf2($this->password, $salt, $this->key_length, $this->iterations, $this->digest_algo);
        if (mb_strlen($key, '8bit') != $this->cipher_size) {
            thrw('Incorrect cipher key length');
        }
        return $key;
    }

    /**
     * Encrypt the given payload.
     *
     * @param  mixed  $payload
     */
    public function encrypt($payload)
    {
        $data = json_encode($payload);
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $salt = random_bytes($this->salt_length);
        $key = $this->generate_key($salt);
        $tag = "";
        $encrypted = openssl_encrypt($data, $this->cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", $this->tag_length);

        if ($encrypted === false) {
            thrw('Could not encrypt the data.');
        }

        return base64_encode($iv . $salt . $encrypted . $tag);
    }

    /**
     * Decrypt the given payload.
     *
     * @param  mixed  $payload
     */
    public function decrypt($data)
    {

        $data = base64_decode($data);
        $iv_length = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 0, $iv_length);
        $salt = substr($data, $iv_length, $this->salt_length);
        $enc_data_and_tag = substr($data, $iv_length + $this->salt_length);
        $enc_data = substr($enc_data_and_tag, 0, (strlen($enc_data_and_tag) - $this->tag_length));
        $tag = substr($enc_data_and_tag, strlen($enc_data));

        $key = $this->generate_key($salt);
        $decrypt = openssl_decrypt($enc_data, $this->cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($decrypt === false) {
            thrw('Could not decrypt the data.', 9001);
        }
        return json_decode($decrypt, true);
    }
}
