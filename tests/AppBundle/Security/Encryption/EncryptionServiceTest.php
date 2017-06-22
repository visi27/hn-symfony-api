<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace Tests\AppBundle\Security\Encryption;

use AppBundle\Security\Encryption\EncryptionService;
use AppBundle\Test\ContainerDependableTestCase;

class EncryptionServiceTest extends ContainerDependableTestCase
{
    public function testEncryptDecrypt()
    {
        /**
         * @var EncryptionService
         */
        $encryptionService = $this->get('app.security.encryption_service');
        $toEncrypt = 'TESTING ENCRYPTION';
        $encrypted = $encryptionService->encrypt($toEncrypt);
        $decrypted = $encryptionService->decrypt($encrypted);

        $this->assertSame($toEncrypt, $decrypted);
        $this->assertNotEmpty($encrypted);
        $this->assertNotEmpty($decrypted);

        $encrypted = 'CHANGED VALUE';
        $decrypted = $encryptionService->decrypt($encrypted);
        $this->assertEmpty($decrypted);
    }
}
