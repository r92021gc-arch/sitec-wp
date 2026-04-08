<?php get_header(); ?>
<main id="single-service" class="py-16 md:py-20">
	<div class="mx-auto max-w-7xl px-4">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article <?php post_class('prose prose-slate max-w-none'); ?>>
			<header class="mb-8">
				<h1 class="text-3xl md:text-4xl font-bold leading-tight"><?php the_title(); ?></h1>
				<?php
                $acf_icon = function_exists('get_field') ? get_field('icon') : null;
                $acf_icon_url = !empty($acf_icon['url']) ? $acf_icon['url'] : '';
                $acf_icon_alt = !empty($acf_icon['alt']) ? $acf_icon['alt'] : get_the_title();
                if ( has_post_thumbnail() ) : ?>
					<div class="mt-6"><?php the_post_thumbnail('large', ['class' => 'w-full h-auto rounded-lg']); ?></div>
				<?php elseif ($acf_icon_url): ?>
                    <div class="mt-6"><img src="<?php echo esc_url($acf_icon_url); ?>" alt="<?php echo esc_attr($acf_icon_alt); ?>" class="w-full h-auto rounded-lg" /></div>
				<?php endif; ?>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php if ( function_exists('get_field') ) :
				$cta_label = trim((string) get_field('cta_label'));
				$cta_url   = trim((string) get_field('cta_url'));
				if ( $cta_label && $cta_url ) : ?>
					<div class="mt-8">
						<a class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-5 py-3 font-medium text-white hover:bg-emerald-600" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
					</div>
				<?php endif; endif; ?>
		</article>
		<?php endwhile; endif; ?>
	</div>
</main>
<?php get_footer(); ?>


