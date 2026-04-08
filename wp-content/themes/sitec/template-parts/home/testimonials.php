<?php
$q = new WP_Query([
    'post_type'      => 'testimonial',
    'posts_per_page' => 3,
]);
?>
<section class="py-20 md:py-24">
    <div class="mx-auto max-w-7xl px-4">

        <?php
        $testimonials_heading = 'Lo Que Dicen Nuestros Clientes';
        $testimonials_sub     = 'Resultados reales, opiniones genuinas';
        if ( function_exists('get_field') ) {
            $custom = trim((string) get_field('testimonials_heading', get_option('page_on_front')));
            if ($custom !== '') { $testimonials_heading = $custom; }
        }
        ?>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?php echo esc_html($testimonials_heading); ?></h2>
            <p class="mt-3 text-slate-500 text-lg"><?php echo esc_html($testimonials_sub); ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
            <article class="sitec-reveal relative rounded-2xl border border-slate-200 bg-white p-7 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex flex-col">
                <!-- Ícono de comillas -->
                <div class="mb-4 text-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-60" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                </div>
                <!-- Estrellas -->
                <div class="flex gap-0.5 mb-4">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
                <!-- Cita -->
                <blockquote class="text-slate-700 leading-relaxed text-sm flex-1">
                    "<?php echo esc_html(get_the_excerpt()); ?>"
                </blockquote>
                <!-- Autor -->
                <div class="mt-6 flex items-center gap-3 pt-5 border-t border-slate-100">
                    <?php if (function_exists('get_field')):
                        $photo = get_field('photo');
                        if (is_array($photo) && !empty($photo['url'])): ?>
                        <img src="<?php echo esc_url($photo['url']); ?>" alt="<?php echo esc_attr($photo['alt'] ?? ''); ?>" class="h-12 w-12 rounded-full object-cover ring-2 ring-emerald-100" />
                    <?php else: ?>
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                            <?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?>
                        </div>
                    <?php endif; endif; ?>
                    <div>
                        <p class="font-bold text-slate-900 text-sm"><?php the_title(); ?></p>
                        <?php if (function_exists('get_field')): ?>
                        <p class="text-slate-500 text-xs"><?php echo esc_html(get_field('author_role')); ?> — <?php echo esc_html(get_field('company')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); else: ?>
            <p class="col-span-3 text-center text-slate-500 py-12">Pronto añadiremos testimonios.</p>
            <?php endif; ?>
        </div>

    </div>
</section>
