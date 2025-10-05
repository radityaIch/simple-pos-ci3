<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

    public function __construct($params = array())
    {
        parent::__construct($params);
        
        // Override default pagination styling with Bootstrap classes
        $this->full_tag_open = '<nav><ul class="pagination">';
        $this->full_tag_close = '</ul></nav>';
        $this->num_tag_open = '<li class="page-item page-link">';
        $this->num_tag_close = '</li>';
        $this->cur_tag_open = '<li class="page-item active"><span class="page-link">';
        $this->cur_tag_close = '</span></li>';
        $this->next_tag_open = '<li class="page-item page-link">';
        $this->next_tag_close = '</li>';
        $this->prev_tag_open = '<li class="page-item page-link">';
        $this->prev_tag_close = '</li>';
        $this->first_tag_open = '<li class="page-item page-link">';
        $this->first_tag_close = '</li>';
        $this->last_tag_open = '<li class="page-item page-link">';
        $this->last_tag_close = '</li>';
    }
}