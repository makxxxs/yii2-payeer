<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yarcode\payeer;

use yii\base\Widget;

/**
 * Class RedirectForm
 * @package yarcode\payeer
 */
class RedirectForm extends Widget
{
    /** @var Merchant */
    public $merchant;
    public $invoiceId;
    public $amount;
    public $description = '';
    public $currency = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        assert(isset($this->merchant));
        assert(isset($this->invoiceId));
        assert(isset($this->amount));

        $this->currency = $this->currency ?: $this->merchant->currency;
    }

    /**
     * @return string
     */
    public function run()
    {
        $amount = Merchant::normalizeAmount($this->amount);
        $description = base64_encode($this->description);

        $parts = array(
            $this->merchant->shopId,
            $this->invoiceId,
            $amount,
            $this->currency,
            $description,
            $this->merchant->secret,
        );

        $sign = strtoupper(hash('sha256', implode(':', $parts)));

        return $this->render('redirect', [
            'merchant' => $this->merchant,
            'invoiceId' => $this->invoiceId,
            'amount' => $amount,
            'currency' => $this->currency,
            'description' => $description,
            'sign' => $sign,
        ]);
    }
}