<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $CI = & get_instance();//get superglobal
        if ($CI->orderitems->exists($num, $code)) {
            $order = $CI->orderitems->get($num, $code);
            $order->quantity++;
            
            $CI->orderitems->update($order);
        } else {
            $order = $CI->orderitems->create();
            $order->order = $num;
            $order->item = $code;
            $order->quantity = 1;
            
            $CI->orderitems->add($order);
        }
    }

    // calculate the total for an order
    function total($num) {
        $CI = & get_instance();//get superobject
        $items = $CI->orderitems->group($num);
        $total = 0;
        if (count($items) > 0)//if there are actually items
            foreach ($items as $item) {//for each item
                $menuOrder = $CI->menu->get($item->item);
                $total += $item->quantity * $menuOrder->price;//calculate the price
            }
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        $this->data['items'] = $this->orders->details($num);
    }

    // cancel an order
    function flush($num) {
        //$this->orderitems->delete_some($order_num);
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        //check to see if your order has one of each type or not
        $CI = & get_instance();
        $OrderItems = $CI->orderitems->group($num);
        $array = array();
        if (count($OrderItems) > 0)
            foreach ($OrderItems as $item) {
                $menu = $CI->menu->get($item->item);
                $array [$menu->category] = 1;
            }
        if( isset($array ['m']) && isset($array ['d']) && isset($array ['s']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
