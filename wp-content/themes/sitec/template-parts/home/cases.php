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
                    <?php else:
                        $t = mb_strtolower(get_the_title());
                        if (str_contains($t, 'sedena') || str_contains($t, 'defensa') || str_contains($t, 'policía') || str_contains($t, 'militar') || str_contains($t, 'guardia')) {
                            $grad = 'from-slate-700 to-slate-900';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>';
                        } elseif (str_contains($t, 'uagro') || str_contains($t, 'universidad') || str_contains($t, 'tecnológica') || str_contains($t, 'ine')) {
                            $grad = 'from-blue-700 to-indigo-900';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>';
                        } elseif (str_contains($t, 'miner') || str_contains($t, 'extracc') || str_contains($t, 'industri')) {
                            $grad = 'from-amber-700 to-yellow-900';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>';
                        } elseif (str_contains($t, 'hospital') || str_contains($t, 'salud') || str_contains($t, 'médic')) {
                            $grad = 'from-emerald-700 to-teal-900';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>';
                        } elseif (str_contains($t, 'hotel') || str_contains($t, 'plaza') || str_contains($t, 'comercial') || str_contains($t, 'privado')) {
                            $grad = 'from-rose-700 to-pink-900';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>';
                        } else {
                            $grad = 'from-slate-600 to-slate-800';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>';
                        }
                    ?>
                        <div class="w-full h-full bg-gradient-to-br <?php echo $grad; ?> flex items-center justify-center transition-transform duration-500 group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $icon; ?></svg>
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
