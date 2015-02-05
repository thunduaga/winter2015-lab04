<?php

/**
 * Order handler
 * 
 * Implement the different order handling usecases.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Order extends Application {

    function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder() {
        //FIXME
        $order_num = $this->orders->highest() + 1;
        
        $newOrder = $this->orders->create();//makes a new 'orders' table
        $newOrder ->num = $order_num;//set the order num
        $newOrder ->date = date(DATE_ATOM);//set the date to be right now
        $newOrder ->status = 'a';//set status to be open
        $newOrder ->total = 0.0;//total is 0 since we havent selected anything
        $this->orders->add($newOrder );//actually add this new order to the tables 'orders'

        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null) {
        if ($order_num == null)
            redirect('/order/neworder');

        $this->data['pagebody'] = 'show_menu';
        $this->data['order_num'] = $order_num;
        //FIXME
        $order = $this->orders->get($order_num);
        $this->data['title'] = $order_num;

        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

	// Bit of a hokey patch here, to work around the problem of the template
	// parser no longer allowing access to a parent variable inside a
	// child loop - used for the columns in the menu display.
	// this feature, formerly in CI2.2, was removed in CI3 because
	// it presented a security vulnerability.
	// 
	// This means that we cannot reference order_num inside of any of the
	// variable pair loops in our view, but must instead make sure
	// that any such substitutions we wish make are injected into the 
	// variable parameters
	// Merge this fix into your origin/master for the lab!
	$this->hokeyfix($this->data['meals'],$order_num);
	$this->hokeyfix($this->data['drinks'],$order_num);
	$this->hokeyfix($this->data['sweets'],$order_num);
	// end of hokey patch
	
        $this->render();
    }

    // inject order # into nested variable pair parameters
    function hokeyfix($varpair,$order) {
	foreach($varpair as &$record)
	    $record->order_num = $order;
    }
    
    // make a menu ordering column
    function make_column($category) {
        return $this->menu->some('category',$category);
    }

    // add an item to an order
    function add($order_num, $item) {
        $this->orders->add_item($order_num, $item);
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num) {
        $this->data['total'] = number_format($this->orders->total($order_num), 2);
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;

        $OrderedItems = $this->orderitems->group($order_num);
        
        foreach ($OrderedItems as $item) 
        {
            $menuitem = $this->menu->get($item->item);
            $item->code = $menuitem->name;
        }
        $this->data['items'] = $OrderedItems;

        $this->data['okornot'] = $this->orders->validate($order_num) ? "" : "disabled";
        $this->render();
    }

    // proceed with checkout
    function proceed($order_num) {
        //FIXME
        if (!$this->orders->validate($order_num))
            redirect('/order/display_menu/' . $order_num);
        $OrderedItems = $this->orders->group($order_num);
        $OrderedItems->status = 'c';
        $OrderedItems->date = date(DATE_ATOM);//set the date to be right now
        $OrderedItems->total = $this->orders->total($order_num);//set the date to be right now
        $this->orders->update($record);
        redirect('/');
    }

    // cancel the order
    function cancel($order_num) {
        //FIXME
        //flush($order_num);
        //couldnt use flush to delete so i put it here
        $this->orderitems->delete_some($order_num);
        
        $Order = $this->orders->get($order_num);
        $Order->status = 'x';//set the status to cancled
        $this->orders->update($Order);
        redirect('/');
        //redirect('/');
    }

}
