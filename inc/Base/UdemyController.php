<?php

/**
 * @package  SLUdemyWPImporter
 */

namespace Inc\Base;

use Inc\Api\CoursesTable;
use Inc\Api\FilesApi;

class UdemyController
{
    public function register()
    {
        add_action('wp_ajax_slui_get_data_from_udemy', array($this, 'getDataFromUdemy'));
        add_action('wp_ajax_nopriv_slui_get_data_from_udemy', array($this, 'getDataFromUdemy'));
        add_action('wp_ajax_slui_add_course_from_udemy', array($this, 'addCourse'));
        add_action('wp_ajax_nopriv_slui_add_course_from_udemy', array($this, 'addCourse'));
    }

    public function getDataFromUdemy()
    {

        // Create Search
        $search = '';
        if (!empty($_POST['search'])){
            $search .= '&search=' . urlencode($_POST['search']);
        }

        if (!empty($_POST['category']) && $_POST['category'] != 'All'){
            $search .= '&category=' . urlencode($_POST['category']);
        }

        // API request
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.udemy.com/api-2.0/courses??fields[course]=title,headline,is_paid,price_detail,primary_category&page_size=200" . $search,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . base64_encode(esc_attr(get_option('slui_client_id')) . ":" . esc_attr(get_option('slui_client_secret')))
            ),
        ));

        $response = curl_exec($curl);
        $responseCode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($responseCode == 200) {
            // Setup data
            $resultsObj = json_decode($response, true);
            $results = $resultsObj['results'];

            $data = array();
            foreach ($results as $result) {
                $data[] = array(
                    'id'          => $result['id'],
                    'title'       => $result['title'],
                    'description' => $result['headline'],
                    'price'       => $result['is_paid'] ? $result['price'] : 'Free',
                    'options'     => '<div id="div-' . $result['id'] . '"><button class="button button-primary" value="Import" onClick="importCourse(' . $result['id'] . ')">Import</button></div>'
                );
            }

            // Prepare table
            $table = new CoursesTable($data);
            $table->data = $data;
            $table->prepare_items();

            echo $table->display();
        } else {
            $resultsObj = json_decode($response, true);
            echo '
            <div class="error notice is-dismissible" >
                <p><strong>Error!</strong> API request failed. ' . $resultsObj['detail'] . '</p>
            </div>
            ';
        }

        wp_die();
    }

    public function addCourse()
    {
        // Fetch course details from the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.udemy.com/api-2.0/courses/" . urlencode($_POST['id']) . "/?fields[course]=title,headline,description,published_title,image_480x270,is_paid,price_detail,primary_category",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . base64_encode(esc_attr(get_option('slui_client_id')) . ":" . esc_attr(get_option('slui_client_secret')))
            ),
        ));

        $response = curl_exec($curl);
        $responseCode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($responseCode == 200) {
            $resultsObj = json_decode($response, true);

            // Get price markup
            $price_markup = esc_attr(get_option('slui_price_markup'))/100;

            // Status
            $status = esc_attr(get_option('slui_import_active'));

            // Create course
            $id = wp_insert_post(array(
                'post_title' => $resultsObj['title'],
                'post_type' => 'courses',
                'post_content' => $resultsObj['description'],
                'post_excerpt' => $resultsObj['headline'],
                'guid' => $resultsObj['published_title'] . $resultsObj['id'],
                'post_status' => $status ? 'publish' : ''
            ));

            // Add featured image
            FilesApi::generateFeaturedImage($resultsObj['image_480x270'], $id);

            // Add Video
            $add_video = esc_attr(get_option('slui_insert_video'));
            $video_id = esc_attr(get_option('slui_video_id'));

            if ($add_video && !empty($video_id)){
                $video = array(
                    'source' => 'html5',
                    'source_video_id' => $video_id,
                    'poster' => null,
                    'source_external_url' => null,
                    'source_youtube' => null,
                    'source_vimeo' => null,
                    'source_embedded' => null,
                );
                update_post_meta($id, '_video', $video);
            }

            // Add category
            $category = $resultsObj['primary_category']['title'];
            wp_insert_term(
                $category,
                'course-category'
            );

            wp_set_object_terms($id, $category, 'course-category');

            // Calculate product price
            if ($resultsObj['is_paid']){
                $course_price = $resultsObj['price_detail']['amount'] + $resultsObj['price_detail']['amount'] * $price_markup;
                update_post_meta($id, '_tutor_course_price_type', 'paid');
            }else{
                $course_price = '';
                update_post_meta($id, '_tutor_course_price_type', 'free');
            }

            // Insert to WooCommerce
            $productObj = new \WC_Product();
            $productObj->set_name($resultsObj['title']);
            $productObj->set_status('publish');
            $productObj->set_price($course_price);
            $productObj->set_regular_price($course_price);
            $productObj->set_sku($resultsObj['id']);

            // Create WooCommerce Category
            $tag = array();
            if (!term_exists($category, 'product_cat')) {
                $term = wp_insert_term($category, 'product_cat');
                array_push($tag, $term['term_id']);
            } else {
                $term_s = get_term_by('name', $category, 'product_cat');
                array_push($tag, $term_s->term_id);
            }
            $productObj->set_category_ids($tag);

            $product_id = $productObj->save();

            // Update post meta
            if ($product_id) {
                update_post_meta($id, '_tutor_course_product_id', $product_id);
                update_post_meta($product_id, '_virtual', 'yes');
                update_post_meta($product_id, '_tutor_product', 'yes');

                $coursePostThumbnail = get_post_meta($id, '_thumbnail_id', true);
                if ($coursePostThumbnail) {
                    set_post_thumbnail($product_id, $coursePostThumbnail);
                }
            }

            echo $id;
        } else {
            echo 0;
        }

        wp_die();
    }

    
}
