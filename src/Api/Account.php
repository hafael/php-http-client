<?php

namespace Hafael\HttpClient\Api;

use Hafael\HttpClient\Route;

class Account extends Api
{

    /**
     * GetAccountList
     * 
     * @param int $pageSize
     * @param int $index
     * @return mixed
     */
    public function getAccountList($pageSize = 10, $index = 1)
    {
        return $this->client->post(new Route(), $this->getBody([
            'Method'   => 'GetAccountList',
            'PageSize' => $pageSize,
            'Index'    => $index,
        ]));
    }

    /**
     * GetAccount
     * 
     * @param string $identifierValue
     * @param string $fieldName TaxNumber or AccountKey
     * @return mixed
     */
    public function getAccount($identifierValue, $fieldName = "TaxNumber")
    {
        return $this->client->post(new Route(), $this->getBody([
            'Method'   => 'GetAccount',
            $fieldName => $identifierValue,
        ]));
    }

}