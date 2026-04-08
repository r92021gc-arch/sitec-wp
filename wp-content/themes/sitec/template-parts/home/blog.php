<?php
$count = 3;
if ( function_exists('get_field') ) {
    $c = (int) get_field('count_posts', get_option('page_on_front'));
    if ($c > 0) { $count = $c; }
}
$orderby      = 'date';
$order        = 'DESC';
$category_ids = [];
if ( function_exists('get_field') ) {
    $ob = trim((string) get_field('posts_orderby', get_option('page_on_front')));
    if ($ob !== '') { $orderby = $ob; }
    $od = trim((string) get_field('posts_order', get_option('page_on_front')));
    if (in_array($od, ['ASC','DESC'], true)) { $order = $od; }
    $raw_terms = get_field('posts_categories', get_option('page_on_front'));
    if (!empty($raw_terms)) {
        $category_ids = array_values(array_filter(array_map('intval', (array) $raw_terms)));
    }
}
$args = [
    'post_type'      => 'post',
    'posts_per_page' => $count,
    'orderby'        => $orderby,
    'order'          => $order,
];
if ( function_exists('get_field') ) {
    $meta_key = trim((string) get_field('posts_meta_key', get_option('page_on_front')));
    if ($meta_key !== '') {
        $args['meta_key'] = $meta_key;
        $args['orderby']  = 'meta_value';
    }
}
if (!empty($category_ids)) {
    $args['category__in'] = $category_ids;
}
$q = new WP_Query($args);
?>
<section class="py-20 md:py-24">
    <div class="mx-auto max-w-7xl px-4">

        <?php
        $blog_heading = 'Insights y Tendencias';
        $blog_sub     = 'Conocimiento técnico aplicado a infraestructuras reales';
        if ( function_exists('get_field') ) {
            $custom = trim((string) get_field('blog_heading', get_option('page_on_front')));
            if ($custom !== '') { $blog_heading = $custom; }
        }
        ?>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?php echo esc_html($blog_heading); ?></h2>
                <p class="mt-2 text-slate-500"><?php echo esc_html($blog_sub); ?></p>
            </div>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="flex-shrink-0 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                Ver todos los artículos
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
            <article class="sitec-reveal group flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
                <div class="relative overflow-hidden h-48 bg-slate-100">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105']); ?>
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                    <?php endif; ?>
                    <!-- Fecha -->
                    <div class="absolute bottom-3 left-3">
                        <span class="rounded-full bg-slate-900/70 backdrop-blur-sm px-3 py-1 text-xs font-medium text-white"><?php echo get_the_date('d M Y'); ?></span>
                    </div>
                </div>
                <div class="flex flex-col flex-1 p-5">
                    <h3 class="font-bold text-slate-900 text-base leading-snug"><?php the_title(); ?></h3>
                    <p class="mt-2 text-slate-500 text-sm leading-relaxed flex-1"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <a class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors" href="<?php the_permalink(); ?>">
                        Leer artículo
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); else: ?>
            <p class="col-span-3 text-center text-slate-500 py-12">Pronto añadiremos artículos.</p>
            <?php endif; ?>
        </div>

    </div>
</section>
