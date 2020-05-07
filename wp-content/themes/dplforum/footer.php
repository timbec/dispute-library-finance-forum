        <!-- Footer
        ============================================= -->
        <footer id="footer" class="dark">

            <!-- Copyrights
            ============================================= -->
            <div id="copyrights">

                <div class="container clearfix">

                    <div class="col_half">
                        <?php echo get_theme_mod( 'ju_footer_copyright_text' ); ?><br>
                        <?php

                        if( get_theme_mod( 'ju_report_file' ) ){
                            ?><a href="<?php echo get_theme_mod( 'ju_report_file' ); ?>">Download Report</a><br><?php
                        }

                        ?>
                        <div class="copyright-links">
                            <?php

                            if( get_theme_mod( 'ju_footer_tos_page' ) ){
                                ?><a href="<?php the_permalink( get_theme_mod( 'ju_footer_tos_page' ) ); ?>">Terms of Use</a><?php
                            }

                            ?> /
                            <?php

                            if( get_theme_mod( 'ju_footer_privacy_page' ) ){
                                ?><a href="<?php the_permalink( get_theme_mod( 'ju_footer_privacy_page' ) ); ?>">Privacy Policy</a><?php
                            }

                            ?>
                        </div>
                    </div>

                    <div class="col_half col_last tright">
                        <div class="fright clearfix">
                            <?php

                            if( get_theme_mod( 'ju_facebook_handle' ) ){
                                ?>
                                <a href="https://facebook.com/<?php echo get_theme_mod( 'ju_facebook_handle' ); ?>" class="social-icon si-small si-borderless si-facebook">
                                    <i class="icon-facebook"></i>
                                    <i class="icon-facebook"></i>
                                </a>
                                <?php
                            }

                            if( get_theme_mod( 'ju_twitter_handle' ) ){
                                ?>
                                <a href="https://twitter.com/<?php echo get_theme_mod( 'ju_twitter_handle' ); ?>" class="social-icon si-small si-borderless si-twitter">
                                    <i class="icon-twitter"></i>
                                    <i class="icon-twitter"></i>
                                </a>
                                <?php
                            }

                            ?>
                        </div>

                        <div class="clear"></div>

                        <?php

                        if( get_theme_mod( 'ju_email' ) ){
                            ?><i class="icon-envelope2"></i> <?php echo get_theme_mod( 'ju_email' );
                        }

                        ?>

                        <span class="middot">&middot;</span>

                        <?php

                        if( get_theme_mod( 'ju_email' ) ){
                            ?><i class="icon-headphones"></i> +<?php echo get_theme_mod( 'ju_phone_number' );
                        }

                        ?>
                    </div>

                </div>

            </div><!-- #copyrights end -->

        </footer><!-- #footer end -->
    </div><!-- #wrapper end -->

    <!-- Go To Top
    ============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>

    <!-- External JavaScripts
    ============================================= -->
    <?php wp_footer(); ?>
</body>
</html>