<?php

use Faker\Factory;
use Nusait\Nuldap\NuldapFake;

class NuldapFakeTest extends PHPUnit_Framework_TestCase
{
    protected $ldap;

    public function setUp()
    {
        parent::setUp();
        $faker = Factory::create();
        $this->ldap = new NuldapFake($faker);
    }

    public function testSearchNetidReturnsAUser()
    {
        $result = $this->ldap->searchNetid('asdf');
        $this->assertEquals($result['uid'][0], 'asdf');
        $this->assertLdapUserKeysExist($result);
    }

    public function testSearchEmplidReturnsAUser()
    {
        $result = $this->ldap->searchEmplid(12345);
        $this->assertEquals($result['employeenumber'][0], 12345);
        $this->assertLdapUserKeysExist($result);
    }

    public function testSearchingNotFoundNetidReturnsNull()
    {
        $result = $this->ldap->searchNetid('nf-user');
        $this->assertNull($result);
    }

    public function testSearchingNotFoundEmplidReturnsNull()
    {
        $result = $this->ldap->searchEmplid('nf-user');
        $this->assertNull($result);
    }

    public function testParseUserWithDefaultTransformerReturnsCorrectUser()
    {
        $result = $this->ldap->searchNetid('asdf');
        $parsed = $this->ldap->parseUser($result);
        $this->assertDefaultTransformerKeysExist($parsed);
    }

    protected function assertLdapUserKeysExist($user)
    {
        $keys = [
            "uid",
            "telephonenumber",
            "mail",
            "title",
            "givenname",
            "sn",
            "displayname",
            "employeenumber",
            "nustudentnumber",
            "jpegphoto"
        ];
        $this->assertEquals($keys, array_keys($user));
    }

    protected function assertDefaultTransformerKeysExist($user)
    {
        $keys = [
            'netid',
            'phone',
            'email',
            'title',
            'first_name',
            'last_name',
            'displayname',
            'emplid',
            'studentid'
        ];
        $this->assertEquals($keys, array_keys($user));
    }
}