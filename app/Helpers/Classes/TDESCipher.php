<?php

namespace App\Helpers\Classes;

use Laravel\Nova\Makeable;

class TDESCipher
{
    use Makeable;

    /**
     * @param string|null $key
     * @param string|null $iv
     *
     * @throws \Exception
     */
    public function __construct(private ?string $key = null, private ?string $iv = null)
    {
        $this->loadTDESCipherInfo();
    }

    public function encrypt(string $input, string $key = '', string $iv = ''): string
    {
        $this->key = $key ?: $this->key;
        $this->iv = $iv ?: $this->iv;

        $payload =
            openssl_encrypt(
                $input,
                "des-ede3-cbc",
                $this->key,
                OPENSSL_RAW_DATA,
                $this->iv
            );

        return static::prepareString(
            str_rot13(
                base64_encode(
                    $payload
                )
            ),
            false
        );
    }

    public function decrypt(string $encrypted, string $key = '', string $iv = ''): bool|string
    {
        $this->key = $key ?: $this->key;
        $this->iv = $iv ?: $this->iv;

        return
            openssl_decrypt(
                base64_decode(
                    str_rot13(
                        static::prepareString($encrypted, true)
                    )
                ),
                'des-ede3-cbc',
                $this->key,
                OPENSSL_RAW_DATA,
                $this->iv
            );
    }

    /**
     * @return $this
     */
    public function loadTDESCipherInfo(): static
    {
        $config = array_wrap(config('hashing.tdes-cipher', []));
        $this->key ??= data_get($config, 'key', $this->key);
        $this->iv ??= data_get($config, 'iv', $this->iv);

        if( !$this->key || strlen($this->key) != 24 ) {
            throw new \Exception("Wrong KEY length, the length must be 24");
        }

        if( !$this->iv || strlen($this->iv) != 8 ) {
            throw new \Exception("IV length error, length should be 8");
        }

        return $this;
    }

    public static function e(string $string): string
    {
        return static::make()->encrypt($string);
    }

    public static function d(string $string): string
    {
        return static::make()->decrypt($string);
    }

    private static function prepareString(string $input, bool $flip = false): string
    {
        $tokens = [
            'Z1' => '=',
            '=' => 'Z1',
            '/' => 'Z2',
            '\\' => 'Z3',
            '+' => 'Z4',
        ];

        foreach( $tokens as $key => $value ) {
            $input = str_ireplace($flip ? $value : $key, $flip ? $key : $value, $input);
        }

        return $input;
    }
}
