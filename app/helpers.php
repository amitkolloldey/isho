<?php

// Converting Name to Seo Url
use App\Product;

if (!function_exists('seoUrl')) {
    function seoUrl($string)
    {
        // Lower case everything
        $string = strtolower($string);
        // Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        // Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }
}

if (!function_exists('get_product_by_sku')) {
    function get_product_by_sku($string)
    {
        $product = Product::where('sku', $string)->get('id')->first();
        if (isset($product)) {
            return $product->id;
        }
        return null;
    }
}

if (!function_exists('get_sku_by_product_id')) {
    function get_sku_by_product_id($id)
    {
        $product = Product::where('id', $id)->get('sku')->first();
        if (isset($product)) {
            return $product->sku;
        }
        return null;
    }
}


if (!function_exists('get_product_attribute_value')) {
    function get_product_attribute_value($id)
    {
        $values = \App\ProductValue::where('attribute_product_id', $id)->get();

        if (isset($values)) {
            return $values;
        }

        return null;
    }
}


if (!function_exists('get_attribute_name_by_id')) {
    function get_attribute_name_by_id($id)
    {
        $values = \App\Attribute::where('id', $id)->get('name')->first();

        if (isset($values)) {
            return $values;
        }

        return null;
    }
}


if (!function_exists('get_attribute_value_by_id')) {
    function get_attribute_value_by_id($id)
    {
        $values = \App\AttributeValue::where('id', $id)->get()->first();

        if (isset($values)) {
            return $values;
        }

        return null;
    }
}


