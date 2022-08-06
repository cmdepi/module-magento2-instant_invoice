<?php
/**
 *
 * @description Invoice management interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 * @note Implementation to provide invoice utils that automate different invoice processes
 *
 */
namespace Bina\InstantInvoice\Api;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;

interface InvoiceManagementInterface
{
    /**
     *
     * Create invoice from order
     *
     * @param Order $order
     * @param bool  $sendEmail
     *
     * @return Invoice
     *
     * @note It is used the order and invoice model instead of their interface because it is required for the implementation logic
     *
     */
    public function create(Order $order, $sendEmail = true);
}
