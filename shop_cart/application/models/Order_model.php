<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Order_model
 * Handles order creation and retrieval.
 */
class Order_model extends CI_Model
{
    protected string $orders_table      = 'orders';
    protected string $order_items_table = 'order_items';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate a unique order number like ORD-20260506-00123
     */
    public function generate_order_number(): string
    {
        $date   = date('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        return "ORD-{$date}-{$random}";
    }

    /**
     * Place a new order.
     * @param array $customer  ['name', 'mobile', 'email']
     * @param array $cart_items  Array of cart rows from session
     * @return string|false  Order number on success, false on failure
     */
    public function place_order(array $customer, array $cart_items): string|false
    {
        $this->db->trans_start();

        $order_number = $this->generate_order_number();
        $total        = array_sum(array_column($cart_items, 'subtotal'));

        // Insert order header
        $this->db->insert($this->orders_table, [
            'order_number' => $order_number,
            'name'         => $customer['name'],
            'mobile'       => $customer['mobile'],
            'email'        => $customer['email'],
            'total_amount' => $total,
        ]);

        $order_id = $this->db->insert_id();

        // Insert order items
        foreach ($cart_items as $item) {
            $this->db->insert($this->order_items_table, [
                'order_id'   => $order_id,
                'product_id' => $item['product_id'],
                'name'       => $item['name'],
                'price'      => $item['price'],
                'quantity'   => $item['qty'],
                'subtotal'   => $item['subtotal'],
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $order_number;
    }

    /**
     * Get order details with items by order number.
     */
    public function get_order_by_number(string $order_number): array|null
    {
        $order = $this->db
            ->where('order_number', $order_number)
            ->get($this->orders_table)
            ->row_array();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->db
            ->where('order_id', $order['id'])
            ->get($this->order_items_table)
            ->result_array();

        return $order;
    }
}
