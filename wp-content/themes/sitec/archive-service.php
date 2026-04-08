
<?php get_header(); ?>
<main id="archive-services" class="py-16 md:py-20">
	<div class="mx-auto max-w-7xl px-4">
		<header class="mb-8">
			<h1 class="text-3xl font-semibold"><?php echo esc_html( post_type_archive_title('', false) ); ?></h1>
			<?php if ( get_the_archive_description() ) : ?>
			<div class="mt-2 text-slate-600"><?php the_archive_description(); ?></div>
			<?php endif; ?>
		</header>
		<?php if ( have_posts() ) : ?>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class('rounded-xl border border-slate-200 p-6 shadow-sm'); ?>>
				<?php
                $acf_icon = function_exists('get_field') ? get_field('icon') : null;
                $acf_icon_url = !empty($acf_icon['url']) ? $acf_icon['url'] : '';
                $acf_icon_alt = !empty($acf_icon['alt']) ? $acf_icon['alt'] : get_the_title();
                if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail('large', ['class' => 'w-full h-48 object-cover rounded-md']); ?>
				<?php elseif ($acf_icon_url): ?>
                    <img src="<?php echo esc_url($acf_icon_url); ?>" alt="<?php echo esc_attr($acf_icon_alt); ?>" class="w-full h-48 object-cover rounded-md" />
				<?php endif; ?>
				<h2 class="mt-4 font-semibold text-lg"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="mt-2 text-slate-600"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<a class="mt-4 inline-flex text-emerald-600 hover:text-emerald-700" href="<?php the_permalink(); ?>">Ver detalle</a>
			</article>
			<?php endwhile; ?>
		</div>
		<div class="mt-8">
			<?php the_posts_pagination([ 'class' => 'pagination' ]); ?>
		</div>
		<?php else: ?>
		<p class="text-slate-600">No hay servicios publicados todavía.</p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>


