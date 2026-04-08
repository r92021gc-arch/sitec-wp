<?php
$count = 6;
if ( function_exists('get_field') ) {
    $c = (int) get_field('count_cases', get_option('page_on_front'));
    if ($c > 0) { $count = $c; }
}
$orderby    = 'date';
$order      = 'DESC';
$sector_ids = [];
if ( function_exists('get_field') ) {
    $ob = trim((string) get_field('cases_orderby', get_option('page_on_front')));
    if ($ob !== '') { $orderby = $ob; }
    $od = trim((string) get_field('cases_order', get_option('page_on_front')));
    if (in_array($od, ['ASC','DESC'], true)) { $order = $od; }
    $raw_terms = get_field('cases_sector', get_option('page_on_front'));
    if (!empty($raw_terms)) {
        $sector_ids = array_values(array_filter(array_map('intval', (array) $raw_terms)));
    }
}
$args = [
    'post_type'      => 'case_study',
    'posts_per_page' => $count,
    'orderby'        => $orderby,
    'order'          => $order,
];
if ( function_exists('get_field') ) {
    $meta_key = trim((string) get_field('cases_meta_key', get_option('page_on_front')));
    if ($meta_key !== '') {
        $args['meta_key'] = $meta_key;
        $args['orderby']  = 'meta_value';
    }
}
if (!empty($sector_ids)) {
    $args['tax_query'] = [[
        'taxonomy' => 'sector',
        'field'    => 'term_id',
        'terms'    => $sector_ids,
    ]];
}
$q = new WP_Query($args);
?>
<section class="py-20 md:py-24 bg-slate-50" id="casos">
    <div class="mx-auto max-w-7xl px-4">

        <?php
        $cases_heading = 'Proyectos que Transforman Infraestructuras Críticas';
        $cases_sub     = 'Casos de éxito reales con impacto medible';
        if ( function_exists('get_field') ) {
            $custom = trim((string) get_field('cases_heading', get_option('page_on_front')));
            if ($custom !== '') { $cases_heading = $custom; }
        }
        ?>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?php echo esc_html($cases_heading); ?></h2>
            <p class="mt-3 text-slate-500 text-lg"><?php echo esc_html($cases_sub); ?></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
            <article class="sitec-reveal group relative rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                <!-- Imagen con overlay en hover -->
                <div class="relative overflow-hidden h-52 bg-slate-200">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105']); ?>
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    <?php endif; ?>
                    <!-- Overlay oscuro en hover -->
                    <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            Ver caso
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </span>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="absolute inset-0" aria-label="<?php the_title_attribute(); ?>"></a>
                </div>
                <!-- Contenido -->
                <div class="flex flex-col flex-1 p-5">
                    <h3 class="font-bold text-slate-900 text-base leading-snug"><?php the_title(); ?></h3>
                    <p class="mt-2 text-slate-500 text-sm leading-relaxed flex-1"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <a class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors" href="<?php the_permalink(); ?>">
                        Ver caso completo
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); else: ?>
            <p class="col-span-3 text-center text-slate-500 py-12">Pronto añadiremos casos de éxito.</p>
            <?php endif; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="<?php echo esc_url( get_post_type_archive_link('case_study') ?: home_url('/cases') ); ?>" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-300 px-6 py-3 font-semibold text-slate-700 hover:border-emerald-500 hover:text-emerald-600 transition-all">
                Descubra Más Casos de Éxito
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </a>
        </div>

    </div>
</section>
