<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06.03.15
 * Time: 10:30
 */

namespace Crm\Models;


class AnalyticsOrders extends CollectionBase
{

    private $_fieldType = array(
        'order_id'=>'int',
        'increment_id'=>'int',
        'store_id'=>'int',
        'customer_id'=>'int',
        'tax_amount'=>'float',
        'shipping_amount'=>'float',
        'discount_amount'=>'float',
        'subtotal'=>'float',
        'grand_total'=>'float',
        'total_qty_ordered'=>'float',
        'total_canceled'=>'float',
        'base_tax_amount'=>'float',
        'base_shipping_amount'=>'float',
        'base_discount_amount'=>'float',
        'base_subtotal'=>'float',
        'base_grand_total'=>'float',
        'base_total_canceled'=>'float',
        'billing_address_id'=>'int',
        'shipping_address_id'=>'int',
        'store_to_base_rate'=>'float',
        'store_to_order_rate'=>'float',
        'base_to_global_rate'=>'float',
        'base_to_order_rate'=>'float',
        'weight'=>'float',
        'quote_id'=>'int',
        'is_virtual'=>'int',
        'customer_group_id'=>'int',
        'customer_note_notify'=>'int',
        'customer_is_guest'=>'int',
        'email_sent'=>'int',
        'parent_id'=>'int',
        'region_id'=>'int',
        'address_id'=>'int',
        'item_id'=>'int',
        'quote_item_id'=>'int',
        'product_id'=>'int',
        'free_shipping'=>'int',
        'is_qty_decimal'=>'int',
        'no_discount'=>'int',
        'qty_canceled'=>'float',
        'qty_invoiced'=>'float',
        'qty_ordered'=>'float',
        'qty_refunded'=>'float',
        'qty_shipped'=>'float',
        'price'=>'float',
        'base_price'=>'float',
        'original_price'=>'float',
        'base_original_price'=>'float',
        'tax_percent'=>'float',
        'tax_invoiced'=>'float',
        'base_tax_invoiced'=>'float',
        'discount_percent'=>'float',
        'discount_invoiced'=>'float',
        'base_discount_invoiced'=>'float',
        'amount_refunded'=>'float',
        'base_amount_refunded'=>'float',
        'row_total'=>'float',
        'base_row_total'=>'float',
        'row_invoiced'=>'float',
        'base_row_invoiced'=>'float',
        'row_weight'=>'float',
        'weee_tax_applied_amount'=>'float',
        'weee_tax_applied_row_amount'=>'float',
        'base_weee_tax_applied_amount'=>'float',
        'base_weee_tax_applied_row_amount'=>'float',
        'weee_tax_disposition'=>'float',
        'weee_tax_row_disposition'=>'float',
        'base_weee_tax_disposition'=>'float',
        'base_weee_tax_row_disposition'=>'float',
        'amount_ordered'=>'float',
        'base_amount_ordered'=>'float',
        'payment_id'=>'int',
        'is_customer_notified'=>'int',
    );

    private function beforeSave()
    {
        foreach ($this as $key => &$value) {
            if (substr($key, 0, 1)=='_') {
                continue;
            }
            $this->setType($key, $value);
        }
    }

    private function setType($key, &$object)
    {
        if (is_array($object) or is_object($object)) {
            foreach ($object as $key => &$value) {
                $this->setType($key, $value);
            }
        } else {
            if ( array_key_exists($key, $this->_fieldType)) {
                settype($object, $this->_fieldType[$key]);
            }
            if ($key=='updated_at' or $key=='created_at'){
                $object = new \MongoDate(strtotime($object));
            }
        }
    }

    static function setPurchase()
    {
        ini_set ('memory_limit', '-1');
        $orderArray = self::find(array(
            "conditions" => array(
                "items.purchase_price" => array(
                    '$exists' => false
                ),
            )
        )
        );
        $count = 0;
        foreach ($orderArray as $order) {
            foreach ($order->items as &$item) {
                if ($item['price']>0){
                    $item['purchase_price']=$item['price']*0.7;
                }else{
                    $product = \Crm\Models\AnalyticsProducts::findFirst(array(array('product_id' => (int)$item['product_id'])));
                    if ($product && $product->price>0){
                        $item['purchase_price']=$product->price*0.7;
                    }else{
                        $item['purchase_price']=9.999;//if you do not set the purchase price
                    }
                }
            }
            $order->save();
            $count=$count+1;
        }
        return $count;
    }

} 