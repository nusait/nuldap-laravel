<?php namespace Nusait\Nuldap;

use Faker\Generator as Faker;
use Nusait\Nuldap\Contracts\LdapInterface;
use Nusait\Nuldap\Contracts\TransformerInterface;
use Nusait\Nuldap\Transformers\DefaultUserTransformer;

/**
 * @method array searchNetid($netid) Searches for a user by netid.
 * @method array searchEmplid($emplid) Searches for a user by emplid.
 * @method array searchEmail($email) Searches for a user by email.
 * @method array searchStudentid($studentid) Searches for a user by student id.
 */
class NuldapFake implements LdapInterface
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;

        return $this;
    }

    public function validate($netid, $password)
    {
        return true;
    }

    public function search($field, $query)
    {
        /*
         * If the query begins wih nf- then return a null user
         * This emulates not finding a user
         */
        $notFoundRegex = '/^nf-(\w+)/';
        if (preg_match($notFoundRegex, $query, $matches)) {
            return null;
        }
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $emplid = in_array($field, ['emplid', 'studentid']) ? $query : $this->faker->numerify('#######');
        return [
            'uid'             => [0 => $field == 'netid' ? $query : $this->faker->bothify('???###')],
            'telephonenumber' => [0 => $this->faker->numerify('555-###-####')],
            'mail'            => [0 => $field == 'email' ? $query : "{$firstName}.{$lastName}@example.com"],
            'title'           => [0 => $this->faker->jobTitle],
            'givenname'       => [0 => $firstName],
            'sn'              => [0 => $lastName],
            'displayname'     => [0 => "{$firstName} {$lastName}"],
            'employeenumber'  => [0 => $emplid],
            'nustudentnumber' => [0 => $emplid],
            'jpegphoto'       => [0 => file_get_contents(__DIR__ . '/../../photo/willie-wildcat.jpg')]
        ];
    }

    public function parseUser($ldapUser, TransformerInterface $transformer = null)
    {
        if (is_null($ldapUser)) {
            return null;
        }
        if (is_null($transformer)) {
            $transformer = new DefaultUserTransformer();
        }

        return $transformer->transform($ldapUser);
    }

    public function __call($name, $arguments)
    {
        $regex = '/^search(\w+)/';
        if (preg_match($regex, $name, $matches)) {
            $field = strtolower(trim($matches[1]));
            if ( ! isset($arguments[0])) {
                throw new \InvalidArgumentException();
            }

            return $this->search($field, $arguments[0]);
        }
        throw new \BadMethodCallException();
    }
}