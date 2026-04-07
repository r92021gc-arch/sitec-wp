<?php
if ( ! defined('ABSPATH') ) { exit; }

// ── Registrar acción AJAX (usuarios y visitantes) ──────────────────────────
add_action('wp_ajax_sitec_chat_lead',        'sitec_chat_lead_handler');
add_action('wp_ajax_nopriv_sitec_chat_lead', 'sitec_chat_lead_handler');

function sitec_chat_lead_handler() {

    // Verificar nonce
    if ( empty($_POST['nonce']) || ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), 'sitec_chat_lead' ) ) {
        wp_send_json_error( ['msg' => 'Nonce inválido.'] );
    }

    // Sanitizar campos
    $nombre  = sanitize_text_field( $_POST['nombre']  ?? '' );
    $email   = sanitize_email(      $_POST['email']   ?? '' );
    $telefono= sanitize_text_field( $_POST['telefono']?? '' );
    $mensaje = sanitize_textarea_field( $_POST['mensaje'] ?? '' );

    if ( ! $nombre || ! $email ) {
        wp_send_json_error( ['msg' => 'Nombre y email son requeridos.'] );
    }
    if ( ! is_email($email) ) {
        wp_send_json_error( ['msg' => 'El email no es válido.'] );
    }

    // Destinatario: correo del admin de WordPress (configurable desde Ajustes > General)
    $to      = get_option('admin_email');
    $subject = '[SITEC Bot] Nuevo lead: ' . $nombre;

    $body  = "Se recibió un nuevo contacto desde el chatbot del sitio web.\n\n";
    $body .= "──────────────────────────────\n";
    $body .= "Nombre:    {$nombre}\n";
    $body .= "Email:     {$email}\n";
    $body .= "Teléfono:  {$telefono}\n";
    if ( $mensaje ) {
        $body .= "Mensaje:\n{$mensaje}\n";
    }
    $body .= "──────────────────────────────\n";
    $body .= "Fecha: " . current_time('d/m/Y H:i') . "\n";
    $body .= "URL origen: " . sanitize_text_field( $_POST['page_url'] ?? '' ) . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $email,
    ];

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( $sent ) {
        // Confirmación al visitante (opcional: enviar copia)
        $confirm_subject = 'Recibimos tu mensaje — SITEC';
        $confirm_body    = "Hola {$nombre},\n\nGracias por contactarnos. Un asesor SITEC se pondrá en contacto contigo en menos de 24 horas.\n\nSi tienes alguna urgencia puedes escribirnos directamente a contacto@sitec.com.mx\n\n— Equipo SITEC\n";
        wp_mail( $email, $confirm_subject, $confirm_body );

        wp_send_json_success( ['msg' => '¡Mensaje enviado! Te contactaremos pronto.'] );
    } else {
        wp_send_json_error( ['msg' => 'No se pudo enviar el mensaje. Intenta de nuevo.'] );
    }
}
