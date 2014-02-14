<?php
/*
Plugin Name: wpcollab Pods Plugin
Version: 0.0.1
License: GPL v2+
*/

class wpcollab_pods_plugin {

    function __construct() {
        add_filter( 'the_content', array( $this, 'content_filter' ) , 20 );
        add_action( 'wp_enqueue_scripts', array( $this, 'style') );
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
            $out = $this->people( $post );
        }
        //Project CPT
        if ( $post->post_type === 'project' ) {
            $out = $this->projects( $post );
        }
        //append $out to content if its safe to.
        if ( $out != FALSE ) {
            $content = $content.$out;
        }
        return $content;
    }



    /**
     * People template
     *
     * @param $post
     */
    function people( $post ) {
        $user = get_userdata( $post->post_author );
        $website = esc_url( $user->user_url );

        $f = get_user_meta( $post->post_author, 'twitter', true );
        if ( $f != '' ) {
            $twitter = 'http://twitter.com/'.$f;
        }
        else {
            $twitter = FALSE;
        }
        $f = get_user_meta( $post->post_author, 'github', true );
        if ( $f != '' ) {
            $github = 'http://github.com/'.$f;
        }
        else {
                $github = FALSE;
        }
        $f = get_user_meta( $post->post_author, 'wporg_username', true);
        if ( $f != '' ) {
            $wp = 'http://profiles.wordpress.org/'.$f;
        }
        else {
                $wp = FALSE;
        }
        $projects = $projects = get_post_meta( $post->ID, 'projects', false );
        echo '<div class="person-info">';
        echo '<ul id="contributor-links" class="wpcp-list">';
                if ( $twitter != FALSE ) {
                    echo '<li><a href="'.$twitter.'"target="_blank" /><span class="genericon genericon-twitter"></span></a></li>';
                }
                if ( $github != FALSE ) {
                    echo '<li><a href="'.$github.'" target="_blank" /><span class="genericon genericon-github"></span></a></li>';
                }
                if ( $wp != FALSE ) {
                    echo '<li><a href="'.$wp.'" target="_blank" /><span class="genericon genericon-wordpress"></span></a></li>';
                }
                if ( $website != '') {
                    echo '<li><a href="'.$website.'" target="_blank" />Website</a></li>';
                }
                echo '</ul>';

                if ( is_array( $projects )   ) :
                    echo '<h5>Projects Contributed To</h5><ul class="wpcp-list">';
                    foreach ( $projects as $proj ) {
                        $id = $proj['ID'];
                        $link = get_permalink( $id );
                        $title = get_the_title( $id );
                        echo '<li><a href="'.$link.'" />'.$title.'</a></li>';
                    }
                    echo '</ul>';
                endif;
                echo '</div>';
    }

    /**
     * Projects Template
     *
     * @param $post
     *
     */
    function projects( $post ) {
        $wp = get_post_meta( $post->ID, 'wporg_link', true );
        $github = get_post_meta( $post->ID,'github_link', true);
        $title = $post->post_title;
        echo '<div class="project-info">';
        ?>
            <h5>Links</h5>
            <ul id="project-links" class="wpcp-list">
                <li><a href="<?php echo $github; ?>" target="_blank" />Github Repo</a></li>
                <li><a href="<?php echo $wp; ?>" target="_blank" />Download From WordPress.org</a></li>
            </ul>
        <?php
        $cs = get_post_meta( $post->ID, 'contributors', false );
        if ( is_array( $cs ) ) {
            echo '<h5>Contributors To '.$title.'</h5>';
            echo '<ul id="proj-contributors" class="wpcp-list">';
            foreach ( $cs as $c ) {
                $id = $c['ID'];
                echo '<li>';
                echo '<div><a href="'.get_permalink( $id ).'">';
                echo get_avatar( $c['post_author'], $default= 'http://images2.wikia.nocookie.net/__cb20130328162041/xkcd-time/images/4/46/Cueball.png').'</div>';
                echo '<div>'.get_the_title( $id );
                echo '</a></div></li>';
            }
        }

        echo '</div>';

    }

    function style() {
        wp_enqueue_style( 'wpcollab-pods', plugin_dir_url( __FILE__ ).'css/wpcollab-pods.css' );
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

