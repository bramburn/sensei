<?php
if ( ! defined( 'ABSPATH' ) ) exit; // security check, don't load file outside WP
/**
 * Sensei Template Class
 *
 * Handles all Template loading and redirecting functionality.
 *
 * @package Sensei
 * @category Templates
 * @since 1.9.0
 */
class Sensei_Templates {

    /**
     *  Load the template files from within sensei/templates/ or the the theme if overrided within the theme.
     *
     * @since 1.9.0
     * @param string $slug
     * @param string $name default: ''
     *
     * @return void
     */
    public static function get_part(  $slug, $name = ''  ){

        $template = '';
        $plugin_template_url = Sensei()->template_url;
        $plugin_template_path = Sensei()->plugin_path() . "/templates/";

        // Look in yourtheme/slug-name.php and yourtheme/sensei/slug-name.php
        if ( $name ){

            $template = locate_template( array ( "{$slug}-{$name}.php", "{$plugin_template_url}{$slug}-{$name}.php" ) );

        }

        // Get default slug-name.php
        if ( ! $template && $name && file_exists( $plugin_template_path . "{$slug}-{$name}.php" ) ){

            $template = $plugin_template_path . "{$slug}-{$name}.php";

        }


        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/sensei/slug.php
        if ( !$template ){

            $template = locate_template( array ( "{$slug}.php", "{$plugin_template_url}{$slug}.php" ) );

        }


        if ( $template ){

            load_template( $template, false );

        }

    } // end get part

    /**
     * Get the template.
     *
     * @since 1.9.0
     *
     * @param $template_name
     * @param array $args
     * @param string $template_path
     * @param string $default_path
     */
    public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

        if ( $args && is_array($args) )
            extract( $args );

        $located = self::locate_template( $template_name, $template_path, $default_path );

        if( ! empty( $located ) ){

            do_action( 'sensei_before_template_part', $template_name, $template_path, $located );

            include( $located );

            do_action( 'sensei_after_template_part', $template_name, $template_path, $located );

        }

    } // end get template

    /**
     * Check if the template file exists. A wrapper for WP locate_template.
     *
     * @since 1.9.0
     *
     * @param $template_name
     * @param string $template_path
     * @param string $default_path
     *
     * @return mixed|void
     */
    public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {

        if ( ! $template_path ) $template_path = Sensei()->template_url;
        if ( ! $default_path ) $default_path = Sensei()->plugin_path() . '/templates/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        // Get default template
        if ( ! $template ){

            $template = $default_path . $template_name;

        }
        // return nothing for file that do not exist
        if( !file_exists( $template ) ){
            $template = '';
        }

        // Return what we found
        return apply_filters( 'sensei_locate_template', $template, $template_name, $template_path );

    } // end locate

    /**
     * template_loader function.
     *
     * @access public
     * @param mixed $template
     * @return void
     */
    public static function template_loader ( $template = '' ) {

        global $wp_query, $email_template;

        $find = array( 'woothemes-sensei.php' );
        $file = '';

        if ( isset( $email_template ) && $email_template ) {

            $file 	= 'emails/' . $email_template;
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( is_single() && get_post_type() == 'course' ) {

            if ( Sensei()->check_user_permissions( 'course-single' ) ) {
                $file 	= 'single-course.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;
            } else {
                // No Permissions Page
                $file 	= 'no-permissions.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;
            } // End If Statement

        } elseif ( is_single() && get_post_type() == 'lesson' ) {

            if ( Sensei()->check_user_permissions( 'lesson-single' ) ) {

                $file 	= 'single-lesson.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;

            } else {

                // No Permissions Page
                $file 	= 'no-permissions.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;

            } // End If Statement

        } elseif ( is_single() && get_post_type() == 'quiz' ) {

            if ( Sensei()->check_user_permissions( 'quiz-single' ) ) {
                $file 	= 'single-quiz.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;
            } else {
                // No Permissions Page
                $file 	= 'no-permissions.php';
                $find[] = $file;
                $find[] = Sensei()->template_url . $file;
            } // End If Statement

        } elseif ( is_single() && get_post_type() == 'sensei_message' ) {

            $file 	= 'single-message.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( is_post_type_archive( 'course' ) || is_page( Sensei()->get_page_id( 'courses' ) ) ) {

            $file 	= 'archive-course.php';


            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( is_post_type_archive( 'sensei_message' ) ) {

            $file 	= 'archive-message.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif( is_tax( 'course-category' ) ) {

            $file 	= 'taxonomy-course-category.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif( is_tax( 'lesson-tag' ) ) {

            $file 	= 'taxonomy-lesson-tag.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( is_post_type_archive( 'lesson' ) ) {

            $file 	= 'archive-lesson.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( isset( $wp_query->query_vars['learner_profile'] ) ) {

            // Override for sites with static home page
            $wp_query->is_home = false;

            $file 	= 'learner-profile.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } elseif ( isset( $wp_query->query_vars['course_results'] ) ) {

            // Override for sites with static home page
            $wp_query->is_home = false;

            $file 	= 'course-results.php';
            $find[] = $file;
            $find[] = Sensei()->template_url . $file;

        } // Load the template file

        if ( $file ) {

            $template = locate_template( $find );
            if ( ! $template ) $template = Sensei()->plugin_path() . '/templates/' . $file;

        } // End If Statement

        return $template;

    } // End template_loader()

    /**
     * Hooks the deprecated archive content hook into the hook again just in
     * case other developers have used it.
     *
     * @deprecated since 1.9.0
     */
    public static function deprecated_archive_hook(){

        /**
         * sensei_course_archive_main_content hook
         *
         * @deprecated since 1.9.0
         *
         * @hooked sensei_course_archive_main_content - 10 (outputs main course archive content loop)
         */
        if( has_action('sensei_course_archive_main_content') ){

            _doing_it_wrong('sensei_course_archive_main_content', 'Sensei: This hook has been retired. Please use sensei_loop_course_before','1.9.0' );

        }

    }// end deprecated_archive_hook

    /**
     * A generic function for echoing the post title
     *
     * @since 1.9.0
     * @param  WP_Post $post
     */
    public static function the_title( $post ){

        /**
         * Filter the template html tag for the title
         *
         * @since 1.9.0
         *
         * @param $title_html_tag default is 'h3'
         */
        $title_html_tag = apply_filters('sensei_the_title_html_tag','h3');

        /**
         * Filter the title classes
         *
         * @since 1.9.0
         * @param string $title_classes defaults to $post_type-title
         */
        $title_classes = apply_filters('sensei_the_title_classes', $post->post_type . '-title' );

        $html= '';
        $html .= '<a href="' . get_permalink( $post->ID ) . '" >';
        $html .= '<'. $title_html_tag .' class="'. $title_classes .'" >';
        $html .= $post->post_title . '</'. $title_html_tag. '>';
        $html .= '</a>';
        echo $html;

    }// end the title

    /**
     * This function adds the hooks inside and above the single course content for
     * backwards compatibility sake.
     *
     * @since 1.9.0
     * @deprecated 1.9.0
     */
    public static function deprecated_single_course_inside_before_hooks(){

        sensei_do_deprecated_action('sensei_course_image','1.9.0', 'sensei_single_course_content_inside_before', array( get_the_ID()) );
        sensei_do_deprecated_action('sensei_course_single_title','1.9.0', 'sensei_single_course_content_inside_before' );
        sensei_do_deprecated_action('sensei_course_single_meta','1.9.0', 'sensei_single_course_content_inside_before' );

    }// end deprecated_single_course_inside_before_hooks

    /**
     * This function adds the hooks to sensei_course_single_lessons for
     * backwards compatibility sake.  and provides developers with an alternative.
     *
     * @since 1.9.0
     * @deprecated 1.9.0
     */
    public static function deprecate_sensei_course_single_lessons_hook(){

        sensei_do_deprecated_action('sensei_course_single_lessons','1.9.0', 'sensei_single_course_content_inside_after');

    }// deprecate_sensei_course_single_lessons_hook

    /**
     * Deprecated all deprecated_single_main_content_hook hooked actions.
     *
     * The content must be dealt with inside the respective templates.
     *
     * @since 1.9.0
     * @deprecated 1.9.0
     */
    public static function deprecated_single_main_content_hook(){

            if( is_singular( 'course' ) ) {

                sensei_do_deprecated_action('sensei_single_main_content', '1.9.0', 'sensei_single_course_content_inside_before or sensei_single_course_content_inside_after');

            } elseif( is_singular( 'message' ) ){

                sensei_do_deprecated_action('sensei_single_main_content', '1.9.0', 'sensei_single_message_content_inside_before or sensei_single_message_content_inside_after');
            }

    }// end deprecated_single_course_single_main_content_hook

    /**
     * Deprecate the  old sensei modules
     * @since 1.9.0
     * @deprecated since 1.9.0
     */
    public static function deprecate_module_before_hook(){

        sensei_do_deprecated_action('sensei_modules_page_before', '1.9.0','sensei_single_course_modules_after' );

    }

    /**
     * Deprecate the  old sensei modules after hooks
     * @since 1.9.0
     * @deprecated since 1.9.0
     */
    public static function deprecate_module_after_hook(){

        sensei_do_deprecated_action('sensei_modules_page_after', '1.9.0','sensei_single_course_modules_after' );

    }

    /**
     * Deprecate the single message hooks for post types.
     *
     * @since 1.9.0
     * @deprecated since 1.9.0
     */
    public static function deprecate_all_post_type_single_title_hooks(){

        if( is_singular( 'sensei_message' ) ){

            sensei_do_deprecated_action( 'sensei_message_single_title', '1.9.0', 'sensei_single_message_content_inside_before' );

        }

    }


}//end class