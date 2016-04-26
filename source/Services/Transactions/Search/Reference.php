<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2016 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace PagSeguro\Services\Transactions\Search;

use PagSeguro\Domains\Account\Credentials;
use PagSeguro\Parsers\Transaction\Search\Date\Request;
use PagSeguro\Resources\Connection;
use PagSeguro\Resources\Http;
use PagSeguro\Resources\Responsibility;

/**
 * Class Payment
 * @package PagSeguro\Services\Checkout
 */
class Reference
{

    /**
     * @param \PagSeguro\Domains\Account\Credentials $credentials
     * @param $reference
     * @param $initial
     * @param $final
     * @param $max
     * @param $page
     * @return string
     * @throws \Exception
     */
    public static function search(
        Credentials $credentials,
        $reference,
        $initial,
        $final = null,
        $max = null,
        $page = null
    ) {

        try {
            $connection = new Connection\Data($credentials);
            $http = new Http();
            $http->get(
                self::request($connection, self::toArray($reference, $initial, $final, $max, $page))
            );

            return Responsibility::http(
                $http,
                new Request
            );

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param Connection\Data $connection
     * @param $params
     * @return string
     */
    private static function request(Connection\Data $connection, $params)
    {
        return sprintf(
            "%1s/?%2s&reference=%3s&initialDate=%4s%5s%6s%7s",
            $connection->buildSearchRequestUrl(),
            $connection->buildCredentialsQuery(),
            $params["reference"],
            $params["initial_date"],
            !isset($params["final_date"]) ?: sprintf("&finalDate=%s", $params["final_date"]),
            !isset($params["max_per_page"]) ?: sprintf("&maxPageResults=%s", $params["max_per_page"]),
            !isset($params["page"]) ?: sprintf("&page=%s", $params["page"])
        );
    }

    /**
     * @param $reference
     * @param $initial
     * @param $final
     * @param $max
     * @param $pages
     * @return array
     */
    private static function toArray($reference, $initial, $final, $max, $pages)
    {
        return [
            'reference' => $reference,
            'initial_date' => $initial,
            'final_date' => $final,
            'max_per_page' => $max,
            'page' => $pages,
        ];
    }
}
