<footer class="bg-slate-900 text-slate-400 mt-0">
	<!-- CTA banda -->
	<div class="border-b border-slate-800 bg-gradient-to-r from-emerald-600 to-teal-700">
		<div class="mx-auto max-w-7xl px-4 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
			<div>
				<h3 class="text-xl md:text-2xl font-extrabold text-white">¿Listo para transformar tu infraestructura?</h3>
				<p class="text-emerald-100 mt-1 text-sm">Agenda una consultoría gratuita con nuestros especialistas.</p>
			</div>
			<a href="<?php echo esc_url( get_permalink( get_page_by_path('contacto') ) ?: home_url('/#contacto') ); ?>" class="flex-shrink-0 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 font-bold text-emerald-700 hover:bg-emerald-50 transition-colors shadow-lg">
				Diagnóstico Gratis
				<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
			</a>
		</div>
	</div>

	<!-- Contenido del footer -->
	<div class="mx-auto max-w-7xl px-4 py-14">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

			<!-- Columna: Marca -->
			<div class="lg:col-span-2">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block font-extrabold text-2xl text-white hover:text-emerald-400 transition-colors">
					<?php bloginfo('name'); ?>
				</a>
				<p class="mt-3 text-slate-400 leading-relaxed max-w-xs">
					Ingeniería, seguridad y telecomunicaciones con resultados comprobados. Más de 20 años transformando infraestructuras críticas en México.
				</p>
				<!-- Redes sociales -->
				<div class="mt-6 flex gap-3">
					<a href="#" class="sitec-social-icon" aria-label="Facebook">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
					</a>
					<a href="#" class="sitec-social-icon" aria-label="Instagram">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
					</a>
				</div>
			</div>

			<!-- Columna: Navegación -->
			<div>
				<h4 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-4">Navegación</h4>
				<?php wp_nav_menu(['theme_location'=>'footer','container'=>false,'menu_class'=>'space-y-2.5','fallback_cb'=>false]); ?>
				<?php if (!has_nav_menu('footer')): ?>
				<ul class="space-y-2.5 text-sm">
					<li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition-colors">Inicio</a></li>
					<li><a href="#servicios" class="hover:text-white transition-colors">Servicios</a></li>
					<li><a href="#casos" class="hover:text-white transition-colors">Casos de Éxito</a></li>
					<li><a href="<?php echo esc_url( get_permalink( get_page_by_path('contacto') ) ?: home_url('/contacto') ); ?>" class="hover:text-white transition-colors">Contacto</a></li>
				</ul>
				<?php endif; ?>
			</div>

			<!-- Columna: Contacto -->
			<div>
				<h4 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-4">Contacto</h4>
				<ul class="space-y-3 text-sm">
					<li class="flex items-start gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-emerald-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
						<span>SITEC Ingeniería<br>Blvd. Vicente Guerrero Km 276, Predio La Cortina<br>Chilpancingo de los Bravo, Guerrero, México</span>
					</li>
					<li class="flex items-start gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-emerald-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
						<a href="mailto:soporte@sitec.solutions" class="hover:text-white transition-colors">soporte@sitec.solutions</a>
					</li>
					<li class="flex items-start gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-emerald-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
						<a href="tel:+527472653472" class="hover:text-white transition-colors">747 265 3472</a>
					</li>
				</ul>
			</div>

		</div>
	</div>

	<!-- Pie inferior -->
	<div class="border-t border-slate-800">
		<div class="mx-auto max-w-7xl px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
			<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Todos los derechos reservados.</p>
			<div class="flex gap-5">
				<a href="<?php echo esc_url( get_permalink( get_page_by_path('privacidad') ) ?: home_url('/privacidad') ); ?>" class="hover:text-white transition-colors">Privacidad</a>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path('terminos') ) ?: home_url('/terminos') ); ?>" class="hover:text-white transition-colors">Términos</a>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
