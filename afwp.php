<?php

/**
 * Plugin Name: ACF for WooCommerce Product
 * Plugin URI: https://github.com/pmbaldha
 * Description: Displays  WooCommerce Product ACF filelds value in front end.
 * Version: 1.8.1
 * Author:      Prashant Baldha
 * Requires at least: 3.8
 * Tested up to: 5.9
 * Author URI: https://prashantwp.com/
 * WC requires at least: 3.4.5
 * WC tested up to: 6.1
* License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * ACF for WooCommerce Product is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.

 * ACF for WooCommerce Product is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with Email Tracker. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/
define( 'AFWP_PATH', plugin_dir_path( __FILE__ ) );
// Exit if accessed directly
if ( !function_exists( 'afwp' ) ) {
    return;
}
// Create a helper function for easy SDK access.
function afwp()
{
    global  $afwp ;
    
    if ( !isset( $afwp ) ) {
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/freemius/start.php';
        $afwp = fs_dynamic_init( array(
            'id'             => '1814',
            'slug'           => 'acf-for-woocommerce-product',
            'type'           => 'plugin',
            'public_key'     => 'pk_51521a906f2dc207465f1c37cf0a0',
            'is_premium'     => true,
            'has_addons'     => false,
            'has_paid_plans' => true,
            'trial'          => array(
            'days'               => 7,
            'is_require_payment' => true,
        ),
            'menu'           => array(
            'slug'       => 'afwp',
            'first-path' => 'admin.php?page=afwp',
        ),
            'is_live'        => true,
        ) );
    }
    
    return $afwp;
}

// Init Freemius.
afwp();
// Signal that SDK was initiated.
do_action( 'afwp_loaded' );
if ( afwp()->is__premium_only() ) {
    if ( afwp()->is_plan( 'pro', true ) || afwp()->is_trial() ) {
        define( 'AFWP_PREMIUM', true );
    }
}
add_action( 'init', 'afwp_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function afwp_load_textdomain()
{
    load_plugin_textdomain( 'afwp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

if ( is_admin() ) {
    require_once AFWP_PATH . 'admin-functions.php';
}
add_action( 'woocommerce_product_meta_end', 'afwp_product_meta' );
function afwp_product_meta()
{
    
    if ( class_exists( 'Acf' ) ) {
        $fields = get_field_objects();
        //var_dump( $fields );
        if ( $fields ) {
            foreach ( $fields as $field_name => $field ) {
                if ( $field_name == '' ) {
                    continue;
                }
                ?>
				<span class="posted_in">
					<?php 
                echo  $field['label'] . ': ' ;
                ?>
                    &nbsp;
                    <?php 
                afwp_woo_render_acf_field( $field );
                ?>
				</span>
                <?php 
            }
        }
    }

}

if ( !function_exists( 'afwp_woo_render_acf_field' ) ) {
    function afwp_woo_render_acf_field( $field )
    {
        if ( afwp()->is__premium_only() ) {
            if ( afwp()->is_plan( 'pro', true ) || afwp()->is_trial() ) {
                switch ( $field['type'] ) {
                    case 'post_object':
                        ?>
						<a href="<?php 
                        echo  get_the_permalink( $field['value']->ID ) ;
                        ?>"><?php 
                        echo  $field['value']->post_title ;
                        ?></a>
						<?php 
                        break;
                    case 'date_picker':
                        echo  date( get_option( 'date_format' ), strtotime( $field['value'] ) ) ;
                        break;
                    case 'file':
                        $file = $field['value'];
                        
                        if ( is_array( $file ) ) {
                            $url = $file['url'];
                            $title = $file['title'];
                            $caption = $file['caption'];
                            if ( $caption ) {
                                ?>
						
								<div class="wp-caption">
						
							<?php 
                            }
                            ?>
						
							<a href="<?php 
                            echo  $url ;
                            ?>" title="<?php 
                            echo  $title ;
                            ?>">
								<span><?php 
                            echo  $title ;
                            ?></span>
							</a>
						
							<?php 
                            
                            if ( $caption ) {
                                ?>
						
									<p class="wp-caption-text"><?php 
                                echo  $caption ;
                                ?></p>
						
								</div>
							<?php 
                            }
                        
                        } elseif ( is_numeric( $file ) ) {
                            ?>
							<a href="<?php 
                            echo  wp_get_attachment_url( intval( $file ) ) ;
                            ?>">
							<?php 
                            echo  wp_get_attachment_url( intval( $file ) ) ;
                            ?>
                            </a>	
							<?php 
                        } else {
                            ?>
							<a href="<?php 
                            echo  $file ;
                            ?>">
                            	<?php 
                            echo  $file ;
                            ?>
                            </a>	
                            <?php 
                        }
                        
                        break;
                    case 'image':
                        //echo $field['value']['sizes']['thumbnail']
                        $image = $field['value'];
                        
                        if ( is_array( $image ) ) {
                            ?>
							<a class="fancybox" rel="group" href="<?php 
                            echo  $image['url'] ;
                            ?>"><img src="<?php 
                            echo  $image['sizes']['thumbnail'] ;
                            ?>" alt="<?php 
                            echo  $image['alt'] ;
                            ?>" />			
							<?php 
                        } elseif ( is_numeric( $image ) ) {
                            echo  wp_get_attachment_image( intval( $image ), 'thumbnail' ) ;
                        } else {
                            ?>
							<a class="fancybox" rel="group" href="<?php 
                            echo  $image ;
                            ?>"><img src="<?php 
                            echo  $image ;
                            ?>" />		
						<?php 
                        }
                        
                        break;
                    case 'wysiwyg':
                        echo  $field['value'] ;
                        break;
                    case 'true_false':
                        
                        if ( $field['value'] ) {
                            esc_html_e( 'Yes', 'afwp' );
                        } else {
                            esc_html_e( 'No', 'afwp' );
                        }
                        
                        break;
                    case 'relationship':
                        $output_array = array();
                        foreach ( $field['value'] as $sub_field ) {
                            if ( is_object( $sub_field ) && is_a( $sub_field, 'WP_Post' ) ) {
                                $output_array[] = '<a href="' . get_the_permalink( $sub_field->ID ) . '">' . $sub_field->post_title . '</a>';
                            }
                            ?>
                       
                        <?php 
                        }
                        echo  implode( ', ', $output_array ) ;
                        break;
                    case 'taxonomy':
                        $output_array = array();
                        
                        if ( is_array( $field['value'] ) ) {
                            foreach ( $field['value'] as $sub_field ) {
                                if ( is_numeric( $sub_field ) ) {
                                    $sub_field = get_term_by(
                                        'id',
                                        intval( $sub_field ),
                                        $field['taxonomy'],
                                        OBJECT
                                    );
                                }
                                if ( is_object( $sub_field ) ) {
                                    $output_array[] = '<a href="' . get_term_link( $sub_field->term_id ) . '">' . $sub_field->name . '</a>';
                                }
                            }
                        } elseif ( is_object( $field['value'] ) ) {
                            $sub_field = $field['value'];
                            $output_array[] = '<a href="' . get_term_link( $sub_field->term_id ) . '">' . $sub_field->name . '</a>';
                        } elseif ( is_numeric( $field['value'] ) ) {
                            $sub_field = get_term_by(
                                'id',
                                intval( $field['value'] ),
                                $field['taxonomy'],
                                OBJECT
                            );
                            $output_array[] = '<a href="' . get_term_link( $sub_field->term_id ) . '">' . $sub_field->name . '</a>';
                        }
                        
                        echo  implode( ', ', $output_array ) ;
                        break;
                    case 'user':
                        
                        if ( $field['field_type'] == 'select' ) {
                            ?>
                        	<br/>
                            <a href="<?php 
                            echo  get_author_posts_url( $field['value']['ID'], $field['value']['user_nicename'] ) ;
                            ?>" title="<?php 
                            echo  $field['value']['user_nicename'] ;
                            ?>" alt="<?php 
                            echo  $field['value']['user_nicename'] ;
                            ?>"><?php 
                            echo  $field['value']['user_avatar'] ;
                            ?></a>
                            
                            
                            <a href="<?php 
                            echo  get_author_posts_url( $field['value']['ID'], $field['value']['user_nicename'] ) ;
                            ?>" title="<?php 
                            echo  $field['value']['user_nicename'] ;
                            ?>" alt="<?php 
                            echo  $field['value']['user_nicename'] ;
                            ?>">   
                            <?php 
                            echo  $field['value']['display_name'] ;
                            ?>
	                       	</a>
						<?php 
                        } else {
                            $output_array = array();
                            foreach ( $field['value'] as $sub_field ) {
                                $output_array[] = '<a href="' . get_author_posts_url( $sub_field['ID'], $sub_field['user_nicename'] ) . '" title="' . $sub_field['user_nicename'] . '" alt="' . $sub_field['user_nicename'] . '">' . $sub_field['user_avatar'] . '</a><br/>
								<a href="' . get_author_posts_url( $sub_field['ID'], $sub_field['user_nicename'] ) . '" title="' . $sub_field['user_nicename'] . '" alt="' . $sub_field['user_nicename'] . '">' . $sub_field['display_name'] . '
	                       		</a>';
                            }
                            echo  '<br/>' . implode( '<br/><br/>', $output_array ) ;
                        }
                        
                        break;
                    case 'color_picker':
                        ?>
						<span style="width:25px; background-color:<?php 
                        echo  $field['value'] ;
                        ?>"><?php 
                        echo  str_repeat( '&nbsp;', 12 ) ;
                        ?></span>
                        <?php 
                        break;
                    default:
                        //select multiple
                        
                        if ( is_array( $field['value'] ) ) {
                            $field_val = $field['value'];
                            array_filter( $field_val );
                            $field_val = array_map( 'trim', $field_val );
                            $field_val = array_map( 'esc_html', $field_val );
                            echo  implode( ', ', $field_val ) ;
                        } else {
                            echo  esc_html( $field['value'] ) ;
                        }
                
                }
            }
        }
        if ( !defined( 'AFWP_PREMIUM' ) ) {
            switch ( $field['type'] ) {
                default:
                    //select multiple
                    
                    if ( is_array( $field['value'] ) ) {
                        $field_val = $field['value'];
                        array_filter( $field_val );
                        $field_val = array_map( 'trim', $field_val );
                        $field_val = array_map( 'esc_html', $field_val );
                        echo  implode( ', ', $field_val ) ;
                    } else {
                        echo  esc_html( $field['value'] ) ;
                    }
            
            }
        }
    }

}