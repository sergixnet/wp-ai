/**
 * Frontend JavaScript for Restaurant Reservations
 */

(function ($) {
  'use strict';

  $(document).ready(function () {
    const form = $('#rr-reservation-form');
    const dateInput = $('#rr-reservation-date');
    const partySizeSelect = $('#rr-party-size');
    const timeSelect = $('#rr-reservation-time');
    const messagesContainer = $('#rr-messages');

    /**
     * Cargar franjas horarias disponibles cuando cambia fecha o comensales
     */
    function loadAvailableSlots() {
      const date = dateInput.val();
      const partySize = partySizeSelect.val();

      if (!date || !partySize) {
        timeSelect.prop('disabled', true);
        timeSelect.html(
          '<option value="">Primero selecciona fecha y comensales</option>'
        );
        return;
      }

      // Mostrar loading
      timeSelect.prop('disabled', true);
      $('.rr-loading').show();
      timeSelect.html('<option value="">Cargando...</option>');

      $.ajax({
        url: rrFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'rr_get_available_slots',
          nonce: rrFrontend.nonce,
          date: date,
          party_size: partySize,
        },
        success: function (response) {
          $('.rr-loading').hide();

          if (response.success) {
            const slots = response.data.slots;
            let options = '<option value="">Selecciona una hora...</option>';

            slots.forEach(function (slot) {
              options += '<option value="' + slot + '">' + slot + '</option>';
            });

            timeSelect.html(options);
            timeSelect.prop('disabled', false);
            showMessage('Franjas horarias disponibles cargadas.', 'success');
          } else {
            timeSelect.html(
              '<option value="">No hay horarios disponibles</option>'
            );
            showMessage(response.data.message, 'error');
          }
        },
        error: function () {
          $('.rr-loading').hide();
          timeSelect.html('<option value="">Error al cargar horarios</option>');
          showMessage('Error al conectar con el servidor.', 'error');
        },
      });
    }

    /**
     * Mostrar mensaje
     */
    function showMessage(message, type) {
      messagesContainer.html(
        '<div class="rr-message ' + type + '">' + message + '</div>'
      );

      // Auto-ocultar mensajes de éxito después de 5 segundos
      if (type === 'success') {
        setTimeout(function () {
          messagesContainer.find('.rr-message').fadeOut();
        }, 5000);
      }
    }

    /**
     * Enviar formulario de reserva
     */
    form.on('submit', function (e) {
      e.preventDefault();

      const submitBtn = form.find('.rr-submit-btn');
      submitBtn.prop('disabled', true).text('Enviando...');
      messagesContainer.empty();

      $.ajax({
        url: rrFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'rr_submit_reservation',
          nonce: rrFrontend.nonce,
          customer_name: $('#rr-customer-name').val(),
          customer_email: $('#rr-customer-email').val(),
          customer_phone: $('#rr-customer-phone').val(),
          party_size: partySizeSelect.val(),
          reservation_date: dateInput.val(),
          reservation_time: timeSelect.val(),
          special_requests: $('#rr-special-requests').val(),
        },
        success: function (response) {
          submitBtn.prop('disabled', false).text('Reservar Mesa');

          if (response.success) {
            showMessage(response.data.message, 'success');
            form[0].reset();
            timeSelect.prop('disabled', true);
            timeSelect.html(
              '<option value="">Primero selecciona fecha y comensales</option>'
            );

            // Scroll to top para ver el mensaje
            $('html, body').animate(
              {
                scrollTop: messagesContainer.offset().top - 100,
              },
              500
            );
          } else {
            showMessage(response.data.message, 'error');
          }
        },
        error: function () {
          submitBtn.prop('disabled', false).text('Reservar Mesa');
          showMessage(
            'Error al conectar con el servidor. Por favor, inténtalo de nuevo.',
            'error'
          );
        },
      });
    });

    // Event listeners
    dateInput.on('change', loadAvailableSlots);
    partySizeSelect.on('change', loadAvailableSlots);
  });
})(jQuery);
