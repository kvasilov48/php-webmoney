<?php

namespace baibaratsky\WebMoney\Api\X\X14;

use baibaratsky\WebMoney\Api\X;
use baibaratsky\WebMoney\Exception\ApiException;
use baibaratsky\WebMoney\Request\RequestSigner;
use baibaratsky\WebMoney\Request\RequestValidator;

/**
 * Class Request
 *
 * @link https://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X14
 */
class Request extends X\Request
{
    /** @var string wmid */
    protected $signerWmid;

    /** @var int trans\inwmtranid */
    protected $transactionId;

    /** @var float trans\amount */
    protected $amount;

    /** @var string trans\moneybackphone */
    protected $phone;

    /** @var string trans\capitallerpursesrc */
    protected $capitallerPurse;

    public function __construct($authType = self::AUTH_CLASSIC)
    {
        if ($authType === self::AUTH_CLASSIC) {
            $this->url = 'https://w3s.webmoney.ru/asp/XMLTransMoneyback.asp';
        } elseif ($authType === self::AUTH_LIGHT) {
            $this->url = 'https://w3s.wmtransfer.com/asp/XMLTransMoneybackCert.asp';
        } else {
            throw new ApiException('This interface doesn\'t support the authentication type given.');
        }

        parent::__construct($authType);
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return array(
            RequestValidator::TYPE_REQUIRED => array('transactionId', 'amount'),
        );
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = '<w3s.request>';
        $xml .= self::xmlElement('reqn', $this->requestNumber);
        $xml .= self::xmlElement('wmid', $this->signerWmid);
        $xml .= self::xmlElement('sign', $this->signature);
        $xml .= '<trans>';
        $xml .= self::xmlElement('inwmtranid', $this->transactionId);
        $xml .= self::xmlElement('amount', $this->amount);
        $xml .= self::xmlElement('moneybackphone', $this->phone);
        $xml .= self::xmlElement('capitallerpursesrc', $this->capitallerPurse);
        $xml .= '</trans>';
        $xml .= '</w3s.request>';

        return $xml;
    }

    /**
     * @return string
     */
    public function getResponseClassName()
    {
        return Response::className();
    }

    /**
     * @param RequestSigner $requestSigner
     *
     * @return void
     */
    public function sign(RequestSigner $requestSigner = null)
    {
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->signature = $requestSigner->sign($this->requestNumber . $this->transactionId . $this->amount);
        }
    }

    /**
     * @return string
     */
    public function getSignerWmid()
    {
        return $this->signerWmid;
    }

    /**
     * @param string $signerWmid
     */
    public function setSignerWmid($signerWmid)
    {
        $this->signerWmid = $signerWmid;
    }

    /**
     * @param int $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (float)$amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $capitallerPurse
     */
    public function setCapitallerPurse($capitallerPurse)
    {
        $this->capitallerPurse = $capitallerPurse;
    }

    /**
     * @return string
     */
    public function getCapitallerPurse()
    {
        return $this->capitallerPurse;
    }
}
