<?php
$count = 4;
if ( function_exists('get_field') ) {
    $c = (int) get_field('count_services', get_option('page_on_front'));
    if ($c > 0) { $count = $c; }
}
$orderby = 'date';
$order = 'DESC';
$include_ids = [];
if ( function_exists('get_field') ) {
    $ob = trim((string) get_field('services_orderby', get_option('page_on_front')));
    if ($ob !== '') { $orderby = $ob; }
    $od = trim((string) get_field('services_order', get_option('page_on_front')));
    if (in_array($od, ['ASC','DESC'], true)) { $order = $od; }
    $raw_ids = trim((string) get_field('services_include_ids', get_option('page_on_front')));
    if ($raw_ids !== '') {
        $parts = preg_split('/[\s,]+/', $raw_ids);
        $include_ids = array_values(array_filter(array_map('intval', $parts)));
    }
}
$args = [
    'post_type'      => 'service',
    'posts_per_page' => $count,
    'orderby'        => $orderby,
    'order'          => $order,
];
if ( function_exists('get_field') ) {
    $meta_key = trim((string) get_field('services_meta_key', get_option('page_on_front')));
    if ($meta_key !== '' && empty($include_ids)) {
        $args['meta_key'] = $meta_key;
        $args['orderby']  = 'meta_value';
    }
    $service_cat_ids = get_field('services_category', get_option('page_on_front'));
    if (!empty($service_cat_ids)) {
        $args['tax_query'] = [[
            'taxonomy' => 'service_category',
            'field'    => 'term_id',
            'terms'    => array_values(array_filter(array_map('intval', (array) $service_cat_ids)))
        ]];
    }
    $service_sector_ids = get_field('services_sector', get_option('page_on_front'));
    if (!empty($service_sector_ids)) {
        $args['tax_query'] = $args['tax_query'] ?? [];
        $args['tax_query'][] = [
            'taxonomy' => 'sector',
            'field'    => 'term_id',
            'terms'    => array_values(array_filter(array_map('intval', (array) $service_sector_ids)))
        ];
    }
    $client_ids = get_field('services_clients', get_option('page_on_front'));
    if (!empty($client_ids)) {
        $client_ids = array_values(array_filter(array_map('intval', (array) $client_ids)));
        $args['meta_query'] = [[
            'key'     => 'related_clients',
            'value'   => $client_ids,
            'compare' => 'IN'
        ]];
    }
}
if (!empty($include_ids)) {
    $args['post__in'] = $include_ids;
    $args['orderby']  = 'post__in';
}
$q = new WP_Query($args);
?>
<section class="py-20 md:py-24" id="servicios">
    <div class="mx-auto max-w-7xl px-4">

        <?php
        $services_heading = 'Nuestros Servicios';
        $services_sub     = 'Soluciones integrales para infraestructuras que no pueden fallar';
        if ( function_exists('get_field') ) {
            $custom = trim((string) get_field('services_heading', get_option('page_on_front')));
            if ($custom !== '') { $services_heading = $custom; }
        }
        ?>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?php echo esc_html($services_heading); ?></h2>
            <p class="mt-3 text-slate-500 text-lg"><?php echo esc_html($services_sub); ?></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
            <article class="sitec-reveal group flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <!-- Imagen -->
                <div class="relative overflow-hidden h-44 bg-slate-100">
                    <?php
                    $acf_icon = function_exists('get_field') ? get_field('icon') : null;
                    $acf_icon_url = !empty($acf_icon['url']) ? $acf_icon['url'] : '';
                    $acf_icon_alt = !empty($acf_icon['alt']) ? $acf_icon['alt'] : get_the_title();
                    if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105']); ?>
                    <?php elseif ($acf_icon_url): ?>
                        <img src="<?php echo esc_url($acf_icon_url); ?>" alt="<?php echo esc_attr($acf_icon_alt); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                    <?php else:
                        $title_lower = mb_strtolower(get_the_title());
                        // Detectar tipo de servicio por palabras clave
                        if (str_contains($title_lower, 'seguridad') || str_contains($title_lower, 'ia') || str_contains($title_lower, 'inteligencia') || str_contains($title_lower, 'protec')) {
                            $grad = 'from-slate-800 to-blue-900';
                            $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>';
                        } elseif (str_contains($title_lower, 'conectiv') || str_contains($title_lower, '5g') || str_contains($title_lower, 'red') || str_contains($title_lower, 'infraestructura') || str_contains($title_lower, 'generaci')) {
                            $grad = 'from-emerald-700 to-teal-900';
                            $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>';
                        } elseif (str_contains($title_lower, 'energ') || str_contains($title_lower, 'eficiencia') || str_contains($title_lower, 'costo') || str_contains($title_lower, 'ahorro')) {
                            $grad = 'from-amber-600 to-orange-800';
                            $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>';
                        } elseif (str_contains($title_lower, 'asesor') || str_contains($title_lower, 'estrateg') || str_contains($title_lower, 'transformac') || str_contains($title_lower, 'consultor')) {
                            $grad = 'from-violet-700 to-purple-900';
                            $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6.75v6.75"/>';
                        } else {
                            $grad = 'from-slate-700 to-slate-900';
                            $icon_svg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>';
                        }
                    ?>
                        <div class="w-full h-full bg-gradient-to-br <?php echo $grad; ?> flex flex-col items-center justify-center gap-3 transition-transform duration-500 group-hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><?php echo $icon_svg; ?></svg>
                        </div>
                    <?php endif; ?>
                    <!-- Tag superior -->
                    <div class="absolute top-3 left-3">
                        <span class="rounded-full bg-emerald-500/90 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-white">Servicio</span>
                    </div>
                </div>
                <!-- Contenido -->
                <div class="flex flex-col flex-1 p-5">
                    <h3 class="font-bold text-slate-900 text-base leading-snug"><?php the_title(); ?></h3>
                    <p class="mt-2 text-slate-500 text-sm leading-relaxed flex-1"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php if ( function_exists('get_field') ):
                        $related_clients = (array) get_field('related_clients');
                        $related_clients = array_values(array_filter(array_map('intval', $related_clients)));
                        if (!empty($related_clients)): ?>
                    <div class="mt-3 flex items-center gap-2">
                        <?php foreach (array_slice($related_clients, 0, 3) as $client_id) {
                            $logo_url = get_the_post_thumbnail_url($client_id, 'thumbnail');
                            if ($logo_url) {
                                echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_the_title($client_id)) . '" class="h-7 w-auto rounded object-contain opacity-70" />';
                            }
                        } ?>
                    </div>
                    <?php endif; endif; ?>
                    <a class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors" href="<?php the_permalink(); ?>">
                        Ver detalle
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); else: ?>
            <p class="col-span-4 text-center text-slate-500 py-12">Pronto añadiremos nuestros servicios.</p>
            <?php endif; ?>
        </div>

    </div>
</section>
