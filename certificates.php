// Breaks a string from one point to another.      
function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);   
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}


// Creates an associative array by pulling in data from certificates posts and user data.
/**
 * Fire a callback only when my-custom-post-type posts are transitioned to 'draft' from another state.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function new_cert_draft( $new_status, $old_status, $certificate ) {
    if ( ( 'draft' === $new_status && 'draft' !== $old_status )
        && 'certificates' === $certificate->post_type) {
        // grab content
        $the_content = $certificate->post_content;

        // create our certificate data array
        $certificate_data = [];

        // parse content and place into array
        $certificate_data["email"] = get_string_between($the_content, "e*", "*e");
        $certificate_data["course_id"] = get_string_between($the_content, "cid*", "*cid");
        $certificate_data["course_name"] = get_string_between($the_content, "cn*", "*cn");
        $certificate_data["first_name"] = get_string_between($the_content, "fn*", "*fn");
        $certificate_data["last_name"] = get_string_between($the_content, "ln*", "*ln");
        $certificate_data["time_completed"] = $certificate->post_title;

        // grab user information to pull professional license numbers
        $user = get_user_by('email', $certificate_data["email"]);

        // add license numbers to certificate array
        $certificate_data["acp_id"] = $user->acupuncture_state_license_number;
        $certificate_data["msg_id"] = $user->massage_therapist_state_license_number;

        // setup our array to be printed
        $content_to_write = print_r($certificate_data, true);

        // created a .txt file with certificate data while testing data parsing / API
        $file = plugin_dir_path( __FILE__ ) . 'new_certificate.txt'; 
        $open = fopen( $file, "a" ); 
        $write = fputs( $open, $content_to_write ); 
        fclose( $open );
        }
    }

add_action( 'transition_post_status', 'new_cert_draft', 10, 3 );
