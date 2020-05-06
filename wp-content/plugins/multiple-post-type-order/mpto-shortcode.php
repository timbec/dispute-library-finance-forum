<?php
// Register Shortcode
function mpto_short_ganerator_eff( $atts ) {
	extract( shortcode_atts( array(
                'post_type'=>'post',
                'posts_per_page'=>'10',
		'category'    => '',
		'style'       => 'style11',
		'google_font' => 'Roboto',
		'des_size'    => '10',
		'des_color'   => '#ffffff',
		'title_size'  => '18',
		'title_color' => '#ffffff',
		'link_open'   => '',
		'item_width'  => '300',
		'item_height' => '300',
		'it_margin'   => '2',
                'meta_key'     => 'custom_order_type_snv_1',
                'limit'     => '250',
                'readmore'     => 'Read More',
	), $atts ) );
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$wp_query = new WP_Query(
		array(  'posts_per_page' => $posts_per_page,
                        'post_type' => $post_type,
                        'post_status' => array( 'publish'),
                        'paged'	=> $paged,
                        'orderby' => 'meta_value_num', 
                        'meta_key' => $meta_key, 
                        'order' => 'ASC',)
	);
        //echo '<pre>';print_r($wp_query);exit;
        $output = '<style type="text/css">
			@import url(http://fonts.googleapis.com/css?family=' . $google_font . ');
			</style>';
        $output .= '<div class="content"><div class="grid">';
	while ( $wp_query->have_posts() ) : $wp_query->the_post();
		$id = get_the_ID();
		$i = 0;
		
			if ( $style == style1 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-lily" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<div>
								<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
								<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							</div>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style2 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-sadie" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style3 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-layla" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style4 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-oscar" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style5 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-marley" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style6 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-ruby" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style7 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-roxy" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style8 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-bubba" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style9 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-romeo" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style10 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-dexter" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style11 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-sarah" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' .get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style12 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-chico" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style13 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-milo" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style14 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-julia" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style15 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-goliath" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style16 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-selena" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style17 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-apollo" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style18 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-steve" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style19 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-ming" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style20 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-jazz" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $text_color . '; font-size:' . $font_size . ';">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style21 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-lexi" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a target="' . $link_open . '" href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			if ( $style == style22 ) {
				$output .= '
					<mpto_figure style="max-width: ' . $item_width . 'px; max-height: ' . $item_height . 'px; margin-right: ' . $it_margin . 'px;" class="effect-duke" id="mpto_bor_box">';
                                        if(get_the_post_thumbnail_url() != ''){
                                                        $output .='<img src="' . get_the_post_thumbnail_url(). '"/>';
                                        }
						$output .='<mpto_figcaption>
							<h2 style="font-size:' . $title_size . 'px; font-family:' . $google_font . '; color:' . $title_color . ';">' . get_the_title() . '</h2>
							<p style="font-family:' . $google_font . '; color:' . $des_color . '; font-size:' . $des_size . 'px;">' . get_excerpt($limit,null,$readmore) . '</p>
							<a href="' . get_the_permalink() . '"></a>
						</mpto_figcaption>			
					</mpto_figure>
				';
			}
			$i ++;
		
	endwhile;
	$output .= '</div></div>';
        $output .='<div class="pagination-mpto">';
                $big = 999999999; // need an unlikely integer
                $pages = paginate_links( array(
                                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                'format' => '?paged=%#%',
                                'current' => max( 1, get_query_var('paged') ),
                                'total' => $wp_query->max_num_pages,
                                'type'  => 'array',
                               // 'prev_next'   => true,
                               // 'prev_text'    => __('« Prev'),
                               // 'next_text'    => __('Next »'),
                        )
                );
                if( is_array( $pages ) ) {
                        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');

                        $pagination = '<ul class="pagination">';

                        foreach ( $pages as $page ) {
                                $pagination .= "<li>$page</li>";
                        }

                        $pagination .= '</ul>';
                        $output .=$pagination;
                }
        $output .='</div>';
        wp_reset_query();

	return $output;
}

add_shortcode( 'mpto', 'mpto_short_ganerator_eff' );
?>