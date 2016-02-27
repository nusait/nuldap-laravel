# NuLdap
This is a LDAP package meant for Northwestern University. (But can be extended for other entities)

## Installation via Composer
```
composer require nusait/nuldap
```

Then update composer
```
composer update
```

## New Instance:
```php
$ldap = new Nusait\Nuldap\NuLdap($rdn, $password, $host, $port);
```
(all parameters are optional);
if you do not put ```$rdn``` or ```$password```, you can still validate, but cannot ```searchNetid```. After instantiating, you can still set the rdn and password with ```setRdn``` and ```setPassword``` respectively.

## Validate:
```php
$ldap->validate($netid, $password);
```
returns a boolean

## Searching:

You can search by netid, email, emplid, or studentid.
```php
$ldap->search('netid', $netid);
$ldap->search('email', $email);
$ldap->search('emplid', $emplid);
$ldap->search('studentid', $studentid);
```
This returns the raw ldap metadata.

You can also search using the magic methods:

```php
$ldap->searchNetid($netid);
$ldap->searchEmail($email);
$ldap->searchEmplid($emplid);
$ldap->searchStudentid($studentid);
```

## Parsing User
```php
$ldap->parseUser($ldapUser [, $transformer ]);
```
You can parse the raw metadata of a user to create an associative array of the user. You can pass your own transformer into the function. The transformer must implement the TransformerInterface in the Contracts folder.

The default transforms maps the following keys and value:

```php
return [
    'netid'       => $this->getSetValueOrNull($ldapUser, 'uid'),
    'phone'       => $this->getSetValueOrNull($ldapUser, 'telephonenumber'),
    'email'       => $this->getSetValueOrNull($ldapUser, 'mail'),
    'title'       => $this->getSetValueOrNull($ldapUser, 'title'),
    'first_name'  => $this->getSetValueOrNull($ldapUser, 'givenname'),
    'last_name'   => $this->getSetValueOrNull($ldapUser, 'sn'),
    'displayname' => $this->getSetValueOrNull($ldapUser, 'displayname'),
    'emplid'      => (int)$this->getSetValueOrNull($ldapUser, 'employeenumber'),
    'studentid'   => (int)$this->getSetValueOrNull($ldapUser, 'nustudentnumber')
];
```