<?php


/**
 * Created by PhpStorm.
 * User: arevkina
 * Date: 06.12.2017
 * Time: 15:39
 */

namespace Compassplus\Sdk\Response;

/**
 * Class Completion
 * @package Compassplus\Response
 */
class Completion extends Response
{
    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->getAttributeName("POSResponse", "f", "t");
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->getAttributeName("POSResponse", "f", "R");
    }

    /**
     * @return string
     */
    public function getApprovalCode()
    {
        return $this->getAttributeName("POSResponse", "f", "F");
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->getAttributeName("POSResponse", "f", "h");
    }
}