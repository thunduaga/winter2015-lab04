<?php

/**
 * Our homepage.
 * 
 * Present a summary of the completed orders.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct() {
        parent::__construct();
    }

    //-------------------------------------------------------------
    //  The normal pages
    //-------------------------------------------------------------

    function index() {
        $this->data['title'] = 'Jim\'s Joint!';
        $this->data['pagebody'] = 'welcome';

        // Get all the completed orders
        //FIXME

        //Some is the function used to get data from the database
        //it returns mixed The selected records, as an array of records
        $completed = $this->orders->some('status', 'c');
        /*$completed is the array of orders
         * $this calls the database table "orders"
         * orders is a table in the database
         * some looks for the 'status' which is an element in the table 'orders'
         * and c means complete, so it looks for each 'orders' table
         * that has the 'status' of 'c'
         * 
         * this is very confusing given there is a model named orders.php
         * a controller named order.php and a table named orders.
         * 
          */
        
        // Build a multi-dimensional array for reporting
        $orders = array();
        foreach ($completed as $order) {
            $this1 = array(
                'num' => $order->num,
                'datetime' => $order->date,
                'amount' => $order->total
            );
            $orders[] = $this1;
        }

        // and pass these on to the view
        $this->data['orders'] = $orders;
        
        $this->render();
    }

}
