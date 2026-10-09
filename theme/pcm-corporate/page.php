<?php get_header(); ?>
<main class="container">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article>
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p>محتوایی برای نمایش وجود ندارد.</p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
