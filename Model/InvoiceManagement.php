<?php
/**
 *
 * @description Invoice management model
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 * @note Implementation to provide invoice utils that automate different invoice processes
 *
 */
namespace Bina\InstantInvoice\Model;

use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Bina\InstantInvoice\Api\InvoiceManagementInterface;

class InvoiceManagement implements InvoiceManagementInterface
{
    /**
     *
     * @var InvoiceSender
     *
     */
    protected $_invoiceSender;

    /**
     *
     * @var TransactionFactory
     *
     */
    protected $_transactionFactory;

    /**
     *
     * Constructor
     *
     * @param InvoiceSender      $invoiceSender
     * @param TransactionFactory $transactionFactory
     *
     */
    public function __construct(
        InvoiceSender      $invoiceSender,
        TransactionFactory $transactionFactory
    ) {
        /**
         *
         * @note Init invoice sender
         *
         */
        $this->_invoiceSender = $invoiceSender;

        /**
         *
         * @note Init transaction factory
         *
         */
        $this->_transactionFactory = $transactionFactory;
    }

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
    public function create(Order $order, $sendEmail = true)
    {
        /**
         *
         * @note Init transaction
         *
         */
        /** @var Transaction $transactionSave */
        $transactionSave = $this->_transactionFactory->create();

        /**
         *
         * @note Prepare invoice
         *
         */
        $invoice = $order->prepareInvoice()->register();

        /**
         *
         * @note It is necessary to set this flag to allow the platform to update order state (example: complete)
         *
         */
        $invoice->getOrder()->setIsInProcess(true);

        /**
         *
         * @note Add objects to save
         *
         */
        $transactionSave->addObject($invoice)->addObject($invoice->getOrder());

        /**
         *
         * @note Save
         *
         */
        $transactionSave->save();

        /**
         *
         * @note Check if it is necessary to send email
         *
         */
        if ($sendEmail) {
            /**
             *
             * @note Send invoice email
             *
             */
            $this->_sendInvoiceEmail($invoice);
        }

        /**
         *
         * @note Return created invoice
         *
         */
        return $invoice;
    }

    /**
     *
     * Send invoice email
     *
     * @param Invoice $invoice
     *
     * @return void
     *
     */
    protected function _sendInvoiceEmail(Invoice $invoice)
    {
        /**
         *
         * @note Send invoice email
         *
         */
        $this->_invoiceSender->send($invoice);

        /**
         *
         * @note Set customer notification flag
         *
         */
        $invoice->getOrder()->setCustomerNoteNotify(true);
    }
}
