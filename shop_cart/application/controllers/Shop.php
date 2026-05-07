<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shop Controller
 * Handles product listing, cart management, checkout and order success.
 */
class Shop extends CI_Controller
{
    // Session key for cart storage
    private const CART_KEY = 'shopping_cart';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Product_model', 'Order_model']);
    }

    // ─────────────────────────────────────────────
    // HOME — Product Listing
    // ─────────────────────────────────────────────
    public function index(): void
    {
        $data = [
            'title'       => 'Our Products',
            'products'    => $this->Product_model->get_all(),
            'cart_count'  => $this->_cart_count(),
        ];
        $this->load->view('shop/layout_header', $data);
        $this->load->view('shop/home', $data);
        $this->load->view('shop/layout_footer');
    }

    // ─────────────────────────────────────────────
    // CART — View Cart
    // ─────────────────────────────────────────────
    public function cart(): void
    {
        $data = [
            'title'      => 'Your Cart',
            'cart_items' => $this->_get_cart(),
            'cart_total' => $this->_cart_total(),
            'cart_count' => $this->_cart_count(),
        ];
        $this->load->view('shop/layout_header', $data);
        $this->load->view('shop/cart', $data);
        $this->load->view('shop/layout_footer');
    }

    // ─────────────────────────────────────────────
    // CART — Add Item (AJAX or POST)
    // ─────────────────────────────────────────────
    public function add_to_cart(): void
    {
        $product_id = (int) $this->input->post('product_id');
        $qty        = max(1, (int) $this->input->post('qty'));

        $product = $this->Product_model->get_by_id($product_id);

        if (!$product) {
            $this->_json_response(false, 'Product not found.');
            return;
        }

        $cart = $this->_get_cart();

        if (isset($cart[$product_id])) {
            $cart[$product_id]['qty']     += $qty;
            $cart[$product_id]['subtotal'] = $cart[$product_id]['qty'] * $cart[$product_id]['price'];
        } else {
            $cart[$product_id] = [
                'product_id' => $product_id,
                'name'       => $product['name'],
                'price'      => (float) $product['price'],
                'qty'        => $qty,
                'image'      => $product['image'],
                'subtotal'   => (float) $product['price'] * $qty,
            ];
        }

        $this->_save_cart($cart);

        $this->_json_response(true, 'Product added to cart.', [
            'cart_count' => $this->_cart_count(),
            'cart_total' => number_format($this->_cart_total(), 2),
        ]);
    }

    // ─────────────────────────────────────────────
    // CART — Update Quantity (AJAX)
    // ─────────────────────────────────────────────
    public function update_cart(): void
    {
        $product_id = (int) $this->input->post('product_id');
        $qty        = (int) $this->input->post('qty');

        $cart = $this->_get_cart();

        if (isset($cart[$product_id])) {
            if ($qty <= 0) {
                unset($cart[$product_id]);
            } else {
                $cart[$product_id]['qty']     = $qty;
                $cart[$product_id]['subtotal'] = $cart[$product_id]['price'] * $qty;
            }
            $this->_save_cart($cart);
        }

        $this->_json_response(true, 'Cart updated.', [
            'cart_count' => $this->_cart_count(),
            'cart_total' => number_format($this->_cart_total(), 2),
            'item_total' => isset($cart[$product_id])
                ? number_format($cart[$product_id]['subtotal'], 2)
                : '0.00',
        ]);
    }

    // ─────────────────────────────────────────────
    // CART — Remove Item
    // ─────────────────────────────────────────────
    public function remove_from_cart(int $product_id): void
    {
        $cart = $this->_get_cart();
        unset($cart[$product_id]);
        $this->_save_cart($cart);

        if ($this->input->is_ajax_request()) {
            $this->_json_response(true, 'Item removed.', [
                'cart_count' => $this->_cart_count(),
                'cart_total' => number_format($this->_cart_total(), 2),
            ]);
        } else {
            redirect('cart');
        }
    }

    // ─────────────────────────────────────────────
    // CHECKOUT — Show Form
    // ─────────────────────────────────────────────
    public function checkout(): void
    {
        $cart = $this->_get_cart();

        if (empty($cart)) {
            redirect('/');
        }

        $data = [
            'title'      => 'Checkout',
            'cart_items' => $cart,
            'cart_total' => $this->_cart_total(),
            'cart_count' => $this->_cart_count(),
        ];
        $this->load->view('shop/layout_header', $data);
        $this->load->view('shop/checkout', $data);
        $this->load->view('shop/layout_footer');
    }

    // ─────────────────────────────────────────────
    // CHECKOUT — Place Order
    // ─────────────────────────────────────────────
    public function place_order(): void
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name',   'Full Name',     'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|regex_match[/^[6-9]\d{9}$/]');
        $this->form_validation->set_rules('email',  'Email ID',      'required|trim|valid_email|max_length[150]');

        $cart = $this->_get_cart();

        if (empty($cart)) {
            redirect('/');
        }

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'title'      => 'Checkout',
                'cart_items' => $cart,
                'cart_total' => $this->_cart_total(),
                'cart_count' => $this->_cart_count(),
            ];
            $this->load->view('shop/layout_header', $data);
            $this->load->view('shop/checkout', $data);
            $this->load->view('shop/layout_footer');
            return;
        }

        $customer = [
            'name'   => $this->input->post('name',   TRUE),
            'mobile' => $this->input->post('mobile', TRUE),
            'email'  => $this->input->post('email',  TRUE),
        ];

        $order_number = $this->Order_model->place_order($customer, array_values($cart));

        if ($order_number) {
            // Clear cart after successful order
            $this->session->unset_userdata(self::CART_KEY);
            redirect('order-success/' . $order_number);
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('checkout');
        }
    }

    // ─────────────────────────────────────────────
    // ORDER SUCCESS
    // ─────────────────────────────────────────────
    public function order_success(string $order_number): void
    {
        $order = $this->Order_model->get_order_by_number($order_number);

        if (!$order) {
            show_404();
        }

        $data = [
            'title'      => 'Order Placed Successfully!',
            'order'      => $order,
            'cart_count' => 0,
        ];
        $this->load->view('shop/layout_header', $data);
        $this->load->view('shop/order_success', $data);
        $this->load->view('shop/layout_footer');
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────

    private function _get_cart(): array
    {
        return $this->session->userdata(self::CART_KEY) ?? [];
    }

    private function _save_cart(array $cart): void
    {
        $this->session->set_userdata(self::CART_KEY, $cart);
    }

    private function _cart_total(): float
    {
        return array_sum(array_column($this->_get_cart(), 'subtotal'));
    }

    private function _cart_count(): int
    {
        return array_sum(array_column($this->_get_cart(), 'qty'));
    }

    private function _json_response(bool $success, string $message, array $data = []): void
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_merge(
                ['success' => $success, 'message' => $message],
                $data
            )));
    }
}
