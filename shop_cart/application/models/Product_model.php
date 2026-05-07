<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product_model
 * Handles all product-related DB operations.
 */
class Product_model extends CI_Model
{
    protected string $table = 'products';

    public function __construct()
    {
        parent::__construct();
    }

    /** Get all active products */
    public function get_all(): array
    {
        return $this->db->get($this->table)->result_array();
    }

    /** Get single product by ID */
    public function get_by_id(int $id): array|null
    {
        $row = $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row_array();

        return $row ?: null;
    }
}
