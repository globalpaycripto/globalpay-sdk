<?php
require('curlRequest.php');

/**
 * Globalpay PHP API
 * version 1.1
 * @link https://globalpay.network/development Official API Documentation.
 * @copyright (c) 2022, GlobalPay
 *
 * Algunos comandos requieren un valor de "moneda". Estos valores se pueden encontrar en
 * https://globalpay.network/coins en la columna código
 *
 * LTCT (Litecoin Testnet) es una moneda de pruebas sin valor comercial y puede ser usada con propositos de desarrollo
 * cuando sea posible con el fin de ahorrar en el costo de transacciones de las monedas reales.
 */
class GlobalpayAPI
{
    private $private_key = '';
    private $token = '';
    private $request_handler;

    /**
     * constructor GlobalpayAPI
     * @param $private_key
     * @param $public_key
     */
    public function __construct($private_key, $token)
    {
        // Clave Privada y token
        $this->private_key = $private_key;
        $this->token = $token;

        // Lanza un error si las claves no se han pasado
        try {
            if (empty($this->private_key) || empty($this->token)) {
                throw new Exception("Your keys are not set!");
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        // Inicializa curl
        $this->request_handler = new CurlRequest($this->private_key, $this->token);
    }

    /**
     * function CreateTransaction
     * Inicia una factura y crea una orden de pago
     *
     * @param integer $amount valor en cripto
     * @param string $currency moneda cripto a recibir
     * @param string  transactionId ID único para referencia y consultas por parte del comercio
     * @param string $redirectUrl URL de destino después de realizada la transacción
     * @param string $buyerEmail Email address for the buyer of the transaction.
     *
     * @return array|object
     * La respuesta exitosa incluye los siguientes valores
     *      - amount            (string)
     *      - address           (string)
     *      - txnId            (string)
     *      - confirmsNeeded   (string)
     *      - timeout           (integer)
     *      - checkoutUrl      (string)
     *      - statusUrl        (string)
     *      - qrcodeUrl        (string)
     *
     * @throws Exception
     */
    public function CreateTransaction($amount, $currency, $transactionId, $redirectUrl, $buyerEmail)
    {
        $fields = [
            'amount' =>  sprintf("%.8f", $amount),
            'currency' => $currency,
            'transactionId' => $transactionId,
            'redirectUrl' => $redirectUrl,
            'buyerEmail' => $buyerEmail
        ];
        return $this->request_handler->execute('createTransaction', $fields);
    }

    /**
     * function GetRates
     * Obtiene las cotizaciones de las monedas en tiempo real
     *
     * @return array|object
     * La respuesta exitosa incluye los siguientes valores:
     *          - Currency           (ejemplo string: BTC)
     *          - rateBtc           (string)
     *          - lastUpdate        (string)
     *          - txFee             (string)
     *          - status             (string)
     *          - name               (string)
     *          - confirms           (string)
     *          - canConvert        (integer)
     *
     * @throws Exception
     */
    public function GetRates()
    {
        return $this->request_handler->execute('rates');
    }

    /**
     * function GetBalance
     * Get balances of all coins, even those with a 0 balance.
     *
     * @return array|object
     * Successful result includes the following values (sample) for each coin.
     *          - Currency          (ejemplo string: BTC)
     *          - balance           (string)
     *          - status            (string)
     *          - enabled           (boolean)
     *          - name              (string)
     *
     * @throws Exception
     */
    public function GetBalance()
    {
        $fields = [
            'all' => 1
        ];
        return $this->request_handler->execute('balance', $fields);
    }


    /**
     * function GetTransactions
     * Obtiene el historial de las transacciones
     *
     * @param string $startDate ? | opcional fecha inicial
     * @param string $endDate ? | opcional fecha final
     * @param string $currency ? | opcional moneda
     * @param string $transactionId ? | opcional ID de la transacción
     *
     * @return array|object Contiene un array con las transacciones
     * @throws Exception
     */
    public function GetTransactions($startDate, $endDate, $currency = null, $transactionId = null)
    {
        $filter = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactionId' => $transactionId,
            'currency' => $currency
        ];
        $fields = ['filter' => $filter];
        return $this->request_handler->execute('transactions', $fields);
    }
}
