<?php

if( post_password_required() ){
    return;
}

?>
<!-- Comments
============================================= -->
<div id="comments" class="clearfix">

    <?php 

    if( have_comments() ){
        ?>
        <h3 id="comments-title"><span><?php comments_number(); ?></span></h3>

        <!-- Comments List
        ============================================= -->
        <ol class="commentlist clearfix">

            <?php

            foreach( $comments as $comment ){
                ?>
                <li class="comment even thread-even depth-1" id="li-comment-1">

                    <div id="comment-1" class="comment-wrap clearfix">

                        <div class="comment-meta">

                            <div class="comment-author vcard">

                                <span class="comment-avatar clearfix">
                                    <?php echo get_avatar( $comment, 60, '', '', [ 'class' => 'avatar avatar-60 photo avatar-default' ] ); ?>
                                </span>

                            </div>

                        </div>

                        <div class="comment-content clearfix">

                            <div class="comment-author">
                                <?php comment_author(); ?>
                                <span><?php comment_date(); ?></span>
                            </div>

                            <?php comment_text(); ?>

                        </div>

                        <div class="clear"></div>

                    </div>

                </li>
                <?php
            }

            the_comments_pagination(); 

            ?>

        </ol><!-- .commentlist end -->
        <?php
    }

    ?>

    <div class="clear"></div>

    <!-- Comment Form
    ============================================= -->
    <div id="respond" class="clearfix">

        <?php

        comment_form([
            'comment_field'         =>  '<div class="clear"></div>

            <div class="col_full">
                <label>Comment</label>
                <textarea name="comment" cols="58" rows="7" class="sm-form-control"></textarea>
            </div>',
            'fields'                =>  [
                'author'            =>  
                    '<div class="col_one_third">
                        <label>' . __( 'Name', 'udemy' ) . '</label>
                        <input type="text" name="author" class="sm-form-control" />
                    </div>',
                'email'             =>  
                    '<div class="col_one_third">
                        <label>' . __( 'Email', 'udemy' ) . '</label>
                        <input type="text" name="email" class="sm-form-control" />
                    </div>',
                'url'               =>  
                    '<div class="col_one_third col_last">
                        <label>' . __( 'Website', 'udemy' ) . '</label>
                        <input type="text" name="url" class="sm-form-control" />
                    </div>'
            ],
            'class_submit'          =>  'button button-3d nomargin',
            'label_submit'          =>  __( 'Submit Comment', 'udemy' ),
            'title_reply'           =>  __( 'Leave a <span>Comment</span>', 'udemy' )
        ]);

        ?>

    </div><!-- #respond end -->

</div><!-- #comments end -->