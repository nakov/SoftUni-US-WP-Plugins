<?php
/**
 * Plugin Name: SoftUni Interactive Engine Shortcodes
 * Plugin URI: https://app.softuni.org
 * Description: Adds shortcodes to connect to the SoftUni Interactive Engine (https://app.softuni.org) with the current user's email.
 *
 * The shortcode [engine course=1 section=3] creates a link to the Interactive engine with the currently logged in user.
 * 
 * The shortcode [set-slack-url url="https://join.slack.com/..."] assigns a global Slack URL for the current course.
 * 
 * The shortcode [slack-url] returns the previously assigned Slack URL for the current course.
 * 
 * The shortcode [hide-element-for-membership levels=3|5|7 id=admission-exam-banner] hides given HTML element (by ID),
 * when the current user has any of the specified membership levels.
 * 
 * Version: 1.0
 * Author: Svetlin Nakov
 * Author URI: https://nakov.com
 */

function create_engine_link_shortcode($attribs) {
    $courseId = $attribs['course'] ?? 0;
    $sectionId = $attribs['section'] ?? 0;
    global $current_user;
    get_currentuserinfo();
    $userEmail = $current_user->user_email;
    global $wp;
    $backUrl = home_url($wp->request);
    global $slackUrl;
    $communityLink = $slackUrl;
    $engineUrl = "https://app.softuni.org/engine/{$courseId}/{$sectionId}?type=course&userId={$userEmail}&back={$backUrl}&community={$communityLink}";
    return $engineUrl;
}

add_shortcode('engine', 'create_engine_link_shortcode');


function set_slack_url_shortcode($attribs) {
    global $slackUrl;
    $slackUrl = $attribs['url'] ?? home_url();
}

add_shortcode('set-slack-url', 'set_slack_url_shortcode');


function get_slack_url_shortcode($attribs) {
    global $slackUrl;
    return $slackUrl;
}

add_shortcode('slack-url', 'get_slack_url_shortcode');


function hide_element_for_membership_shortcode($attribs) {
    if (is_admin()) {
        // Do nothing when in 'edit page' mode
        return "";
    }

    $levels = $attribs['levels'] ?? '';
    $membershipLevels = explode('|', $levels);
    $elementId = $attribs['id'] ?? 'missing-element-id';
    if (pmpro_hasMembershipLevel($membershipLevels)) {
        // Hide the specified element with CSS display:none
        return 
            "<style>
                #{$elementId} {
                    display: none;                
                }
            </style>";
    }
}

add_shortcode('hide-element-for-membership', 'hide_element_for_membership_shortcode');
