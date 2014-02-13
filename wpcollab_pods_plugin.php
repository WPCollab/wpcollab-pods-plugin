<?php
/*
Plugin Name: wpcollab Pods Plugin
Version: 0.0.1
License: GPL v2+
*/

class wpcollab_pods_plugin {

    function __construct() {
        add_filter( 'the_content', array( $this, 'content_filter' ) , 20 );
    }

    /**
     * Control for the output.
     *
     * @since 0.0.1
     */
    function content_filter( $content ) {
        global $post;
        $id = $post->ID;
        //Profile pages are children of page with ID 23
        if ( $post->post_parent === 23 ) {
            $out = $this->make_it_so( $id, 'page', 'people' );
        }
        //Project CPT
        if ( $post->post_type === 'project' ) {
            $out = $this->make_it_so( $id, 'project', 'projects' );
        }
        //For profile pages
        //@TODO NEED a way for this not to break for users with no posts.
        if ( is_author() ) {
            $out = $this->make_it_so( $id, 'user', 'contributors' );
        }
        //append $out to content if its safe to.
        if ( $out != FALSE ) {
            $content = $content.$out;
        }
        return $content;
    }

    /**
     * Creates our output
     *
     * @param int $id Item ID
     * @param string $pod Pod name
     * @param string $template Pod name to get
     *
     * @return bool|string Pods Template output or false if pod, item and template do not all exist.
     */
    function make_it_so( $id, $pod, $template ) {
        $obj = $this->get_pods_object( $id, $pod );
        $it = $this->get_pods_template( $template, $obj );
        /**
         * Filter output
         *
         * Use to do something totally diffrent than Pods Template
         *
         * @param string $it The output
         * @param obj $obj The Pods object
         *
         * @since 0.0.1
         */
        $it = apply_filters( 'wpcollab_pods_plugin_it', $it, $obj );
        return $it;
    }

    /**
     * Setup Pods object
     *
     * @param int $id Item ID
     * @param string $pod Pod name
     *
     * @return bool|obj A Pods object or FALSE if Pod or item in Pod do not exist.
     *
     * @since 0.0.1
     */
    function get_pods_object( $id, $pod ) {
        $obj = pods( $pod, $id, $strict == TRUE; );
        if ( $obj->exists() ) {
            return TRUE;
        }
        return $obj;
    }

    /**
     * Get the Pods Template
     *
     * @param string $template Tempalte Name
     * @param $obj
     *
     * @return bool|string Pods template or FALSE if template doesn't exist.
     *
     * @since 0.0.1
     */
    function get_pods_template( $template, $obj ) {
        $temp = $obj->template( $template );
        if ( isset($temp)  ) {
            return $temp;
        }
        else {
            return FALSE;
        }
    }

}

/**
 * Initialize
 *
 * @TODO Better Pods exists saftey check?
 */
if ( function_exists( 'pods_deactivate_pods_1_x' ) ) {
    new wpcollab_pods_plugin();
}

