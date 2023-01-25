[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# PHP HTTP Client for PHP
------

This library provides developers with a simple set of bindings to help you integrate REST APIs to PHP project`s.

[![Latest Stable Version](http://poser.pugx.org/hafael/php-http-client/v)](https://packagist.org/packages/hafael/php-http-client)
[![Latest Unstable Version](http://poser.pugx.org/hafael/php-http-client/v/unstable)](https://packagist.org/packages/hafael/php-http-client)
[![Total Downloads](http://poser.pugx.org/hafael/php-http-client/downloads)](https://packagist.org/packages/hafael/php-http-client)
[![License](http://poser.pugx.org/hafael/php-http-client/license)](https://packagist.org/packages/hafael/php-http-client)


## 💡 Requirements

PHP 7.3 or higher

## 📦 Installation 

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) if not already installed

2. On your project directory run on the command line
`composer require "hafael/php-http-client"`

3. Extend classes for you want.

PHP HTTP Client successfully installed.


## 🌟 Getting Started
  
  Simple usage looks like:
  
```php
  <?php
    require_once 'vendor/autoload.php'; // You have to require the library from your Composer vendor folder

    use Hafael\HttpClient\Client;

    class MyCustomClient extends Client {

      /**
       * The Client (not Eastwood)
       * 
       * @param string $apiKey
       * @param string $baseUrl
       */
      public function __construct(string $apiKey, string $baseUrl)
      {
          $this->setApiKey($apiKey);
          $this->setBaseUrl($baseUrl);
      }
    }

    $customClient = new MyCustomClient(
        'API_KEY',
        'https://myapiendpoint.com.br',
    );

    //Get Api Method
    $response = $customClient->exampleOfApiResource()
                             ->getExampleResourceMethod();
    
    var_dump($response->json());

  ?>
```


Creating new KYC Account

```php
  <?php
    require_once 'vendor/autoload.php';

    use Hafael\Fitbank\Client;
    use Hafael\Fitbank\Models\Account;
    use Hafael\Fitbank\Models\Address;
    use Hafael\Fitbank\Models\Document;
    use Hafael\Fitbank\Models\Person;

    ...

    //Create new KYC Account
    
    $holder = new Person([
        'personRoleType' => Person::ROLE_TYPE_HOLDER,
        'taxNumber' => '88494940090',
        'identityDocument' => '269435310',
        'personName' => 'Rafael da Cruz Santos',
        'nickname' => 'Rafael',
        'mail' => 'rafaelmail@meuemail.com',
        'phoneNumber' => '219729345534',
        'checkPendingTransfers' => false,
        'publicExposedPerson' => false,
        'birthDate' => '1991/03/20',
        'motherFullName' => 'Daniela Cruz de Marquez',
        'fatherFullName' => 'João Francisco Santos',
        'nationality' => 'Brasileiro',
        'birthCity' => 'Niterói',
        'birthState' => 'Rio de Janeiro',
        'gender' => Person::GENDER_MALE,
        'maritalStatus' => Person::MARITAL_SINGLE,
        'occupation' => 'Empresário',
    ]);

    $documents = [
        Document::fromBase64('dGVzdGU=', Document::FORMAT_JPG)
                ->documentType(Document::TYPE_CNH)
                ->expirationDate('2023/04/15'),
        Document::fromBase64('dGVzdGU=', Document::FORMAT_JPG)
                ->documentType(Document::TYPE_PROOF_ADDRESS),
    ];

    $addresses = [
        new Address([
            'addressType' => Address::RESIDENTIAL,
            'addressLine' => 'Av. Quintino de Bocaiúva',
            'addressLine2' => '61',
            'complement' => 'McDonald`s',
            'zipCode' => '24360-022',
            'neighborhood' => 'São Francisco',
            'cityName' => 'Niterói',
            'state' => 'RJ',
            'country' => 'Brasil',
        ])
    ];

    $account = new Account([
        'holder' => $holder,
        'documents' => $documents,
        'addresses' => $addresses,
    ]);

    $response = $fitbankClient->account->newAccount($account);
    var_dump($response->json());


  ?>
```


## 📚 Documentation 

Visit our Dev Site for further information regarding:
 - Fitbank API Docs: [English](https://dev.fitbank.com.br/docs)


## 📜 License 

MIT license. Copyright (c) 2023 - [Rafael](https://github.com/hafael) / [Fitbank](https://fitbank.com.br)
For more information, see the [LICENSE](https://github.com/hafael/php-http-client/blob/main/LICENSE) file.