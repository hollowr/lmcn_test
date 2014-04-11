<?php
    $args = array(
        'meta_key' => 'sgt_slide',
        'meta_value' => 'on',
        'numberposts' => -1,
        );
    $slides = get_posts($args);
    $c = 0;
    if ( !empty($slides) ) : $exl_posts = Array(); ?>

        <div class="slideshow"><div id="slideshow">

            <?php foreach( $slides as $post ) :

                setup_postdata($post);
                global $exl_posts;
                $exl_posts[] = $post->ID;


            ?>
            <div class="slide clear">
                <div class="posts">
                    <?php if ( has_post_thumbnail() )
                        $slider_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'taille-slider');
                        echo '<a href="'.get_permalink().'" title="'.trim(strip_tags( $post->post_title )).'"><img src="'. $slider_image_url[0].'"></a>';
                    ?>
                    <div class="fullsize" style="">
                        <?php $category = get_the_category();?>
                        <span class="slidecat" style=""><?php echo $category[1]->cat_name; ?></span>
                        <h2 style="padding:20px 13px;width: 90%;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="post-content"><?php if ( has_post_thumbnail() && function_exists('the_excerpt') ) the_excerpt(get_the_excerpt(), 20); else the_excerpt(get_the_excerpt(), 100); ?></div>
                    </div>
                </div>
            </div>
            <?php $c++; endforeach; ?>

        </div>
        <div>
            <a href="javascript: void(0);" id="larr"></a>
            <span> 1 / <?php echo $c;?>
            <a href="javascript: void(0);" id="rarr"></a>
        </div>
    <?php endif; ?>