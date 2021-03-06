<?php
/**
 * Crypt_RSA Library Test
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-10-27
 */
require_once dirname(__FILE__) . '/../../libraries/crypt/RSA.php';

class Crypt_RSATest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->crypt = new Crypt_RSA('/tmp/phpunit/');
    }

    /**
     * @test
     */
    public function createKey() {
        $this->crypt->createKey();
        $this->assertTrue(file_exists('/tmp/phpunit/priv.key'));
        $this->assertTrue(file_exists('/tmp/phpunit/pub.key'));
    }

    /**
     * @test
     */
    public function setupPrivateKey() {
        $setup_result = $this->crypt->setupPrivateKey();
        $this->assertTrue($setup_result);

        $key_exists_setup = $this->crypt->setupPrivateKey();
        $this->assertTrue($key_exists_setup);
    }

    /**
     * @test
     */
    public function setupPublicKey() {
        $setup_result = $this->crypt->setupPublicKey();
        $this->assertTrue($setup_result);

        $key_exists_setup = $this->crypt->setupPublicKey();
        $this->assertTrue($key_exists_setup);
    }

    /**
     * @test
     */
    public function getPublicKeyModulus() {
        file_put_contents('/tmp/phpunit/priv.key', file_get_contents(dirname(__FILE__) . '/../setup/priv.key'));
        $modulus = $this->crypt->getPublicKeyModulus();
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/../setup/pubmodulus'), $modulus);
    }

    /**
     * @test
     */
    public function pubEncryptParamterNotString() {
        $encrypted = $this->crypt->pubEncrypt(array());
        $this->assertEquals(null, $encrypted);
    }

    /**
     * @test
     */
    public function privDecryptParamterNotString() {
        $decrypted = $this->crypt->privDecrypt(array());
        $this->assertEquals(null, $decrypted);
    }

    /**
     * @test
     */
    public function pubEncryptFailure() {
        $this->setupPublicKey();
        $encrypted = $this->crypt->pubEncrypt("0x45d267021a5117a22610953f3cf89b3bca9f9f378ebc757f2840331c0a867b7928a2ebc06c0");
        $this->assertEquals(null, $encrypted);
    }

    /**
     * @test
     */
    public function privDecryptFailure() {
        $this->setupPrivateKey();
        $decrypted = $this->crypt->privDecrypt("aassddttdd");
        $this->assertEquals(null, $decrypted);
    }

    /**
     * @test
     */
    public function pubEncryptAndPrivDecryptSuccess() {
        $encrypted = $this->crypt->pubEncrypt('new message');
        $this->assertEquals('new message', $this->crypt->privDecrypt($encrypted));
    }

    public function tearDown() {
        exec("rm -rf /tmp/phpunit");
        unset($this->crypt);
    }
}