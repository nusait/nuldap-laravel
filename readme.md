# NuLdap
This is a LDAP package meant for Northwestern University. (But can be extended for other entities)

## Installation via Composer
```
composer require nusait/nuldap-laravel
```

Then update composer
```
composer update
```

## Laravel
Add the service provider to `config/app.php`
```php
Nusait\Nuldap\NuldapServiceProvider::class,
```

Publish the config:
```
php artisan vendor:publish --provider="Nusait\Nuldap\NuldapServiceProvider" 
```

To use the fake Ldap, make sure to add to your `.env` file the line:
```
ldap_fake=true
```

When using NuldapFake, any search will return a user with pre-filled data using Faker. Whatever search term you provide for a field will return as the value for that field, i.e. a `searchNetid('asdf')` will return a user with `['netid'] = 'asdf'`. In order to simulate not finding a user, prepend the query with `nf-`, i.e. `searchNetid('nf-blah')` and it will not find that user.

## New Instance:
`$ldap = \App::make('ldap')` or `$ldap = app('ldap')`

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
