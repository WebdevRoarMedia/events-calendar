<?php
// Start of the index.php file

// Include necessary WordPress functions
get_header(); // This function includes the header template part (optional, depending on your theme's structure).

// Define the shortcode function for the events section
function events_section_code() {
    // Get current date in 'Y-m-d' format
    $today = current_time('Y-m-d');

    // Initialize output variable
    $output = '';

    // Query args to fetch events within the date range
    $args = array(
        'post_type' => 'event', // Ensure this matches your actual post type slug
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'event_date',
                'value' => $today,
                'compare' => '<=',
                'type' => 'DATE'
            ),
            array(
                'key' => 'event_end_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATE'
            ),
        ),
    );

    // Execute the query
    $query = new WP_Query($args);

    // Check if any events are found
    if (!$query->have_posts()) {
        $output .= '<p style="display:none;">No events found.</p>';
        return $output; // Return if no events found
    }

    // Start building the output for the events
    $output .= '<div class="events-list">';

    // Loop through the events
    while ($query->have_posts()) {
        $query->the_post();

        // Get the permalink for the event
        $event_link = get_permalink();

        // Get the featured image URL
        $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); // Adjust size as needed

        // Add event details to the output
        $output .= '<div class="event-item">';
        $output .= '<div class="event-item-interior">';
        $output .= '<a href="' . esc_url($event_link) . '">';

        // Display the featured image or a fallback message
        if ($featured_image_url) {
            $output .= '<img src="' . esc_url($featured_image_url) . '" alt="' . esc_attr(get_the_title()) . '" style="max-width: 100%; height: auto;">';
        } else {
            $output .= '<p>No Image Available</p>'; // Optional fallback content
        }

        $output .= '</a>';
        $output .= '</div>'; // Close .event-item-interior
        $output .= '</div>'; // Close .event-item
    }

    // Close the events list div
    $output .= '</div>';

    // Reset post data
    wp_reset_postdata();

    // Return the output
    return $output;
}

// Register the shortcode
add_shortcode('events-section', 'events_section_code');

// You can optionally call the shortcode directly if this file serves as a template
echo do_shortcode('[events-section]');

// Include the footer template part (optional, depending on your theme's structure).
get_footer();
