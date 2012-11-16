<?php
/**
 * Created by JetBrains PhpStorm.
 * User: meabed
 * Date: 11/16/12
 * Time: 1:28 AM
 * To change this template use File | Settings | File Templates.
 */
class Web_Ppfix_Model_Cart extends Mage_Paypal_Model_Cart {
    /**
     * Add a line item
     *
     * @param string $name
     * @param numeric $qty
     * @param float $amount
     * @param string $identifier
     * @return Varien_Object
     */
    public function addItem($name, $qty, $amount, $identifier = null)
    {
        $this->_shouldRender = true;
        $item = new Varien_Object(array(
            'name'   => $name,
            'qty'    => $qty,
            'amount' => (float)$amount,
        ));
        if ($identifier) {
            $item->setData('id', $identifier);
        }
        $this->_items[] = $item;
        return $item;
    }
    public function getTotals($mergeDiscount = false)
    {
        $this->_render();

        // cut down totals to one total if they are invalid
        if (!$this->_areTotalsValid) {
            $totals = array(self::TOTAL_SUBTOTAL =>
            $this->_totals[self::TOTAL_SUBTOTAL] + $this->_totals[self::TOTAL_TAX]
            );
            if (!$this->_isShippingAsItem) {
                $totals[self::TOTAL_SUBTOTAL] += $this->_totals[self::TOTAL_SHIPPING];
            }
            if (!$this->_isDiscountAsItem) {
                $totals[self::TOTAL_SUBTOTAL] -= $this->_totals[self::TOTAL_DISCOUNT];
            }
            return $totals;
        } elseif ($mergeDiscount) {
            $totals = $this->_totals;
            unset($totals[self::TOTAL_DISCOUNT]);
            if (!$this->_isDiscountAsItem) {
                $totals[self::TOTAL_SUBTOTAL] -= $this->_totals[self::TOTAL_DISCOUNT];
            }
            return $totals;
        }
        return $this->_totals;
    }
}