import jQuery from 'jquery';

export default function () {
	const pubguruConnectButton = jQuery('.js-pubguru-connect');
	const spinner = pubguruConnectButton.next('.aa-spinner');
	const metabox = jQuery('#advads-m2-connect');

	jQuery('.js-m2-show-consent').on('click', '.button', function (event) {
		event.preventDefault();

		const tr = jQuery(this).closest('tr');

		metabox.show();
		tr.addClass('hidden');
		tr.next().removeClass('hidden');
	});

	jQuery('.js-pubguru-disconnect').on('click', '.button', function (event) {
		event.preventDefault();

		const tr = jQuery(this).closest('tr');

		metabox.hide();
		tr.addClass('hidden');
		tr.prev().removeClass('hidden');

		jQuery
			.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'pubguru_disconnect',
					nonce: advadsglobal.ajax_nonce,
				},
				dataType: 'json',
			})
			.done(function (response) {
				if (!response.success) {
					return;
				}

				const notice = jQuery('<div class="notice notice-success" />');
				notice.html('<p>' + response.data.message + '</p>');

				tr.closest('.postbox').after(notice);
				setTimeout(function () {
					notice.fadeOut(500, function () {
						notice.remove();
					});
				}, 3000);
			});
	});

	jQuery('#m2-connect-consent').on('change', function () {
		const checkbox = jQuery(this);
		pubguruConnectButton.prop('disabled', !checkbox.is(':checked'));
	});

	jQuery('#advads-overview').on('click', '.notice-dismiss', function (event) {
		event.preventDefault();
		const button = jQuery(this);
		const notice = button.parent();
		notice.fadeOut(500, function () {
			notice.remove();
		});
	});

	pubguruConnectButton.on('click', function (event) {
		event.preventDefault();

		spinner.addClass('show');
		jQuery
			.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'pubguru_connect',
					nonce: advadsglobal.ajax_nonce,
				},
				dataType: 'json',
			})
			.done(function (response) {
				if (!response.success) {
					return;
				}

				jQuery('.pubguru-not-connected').hide();
				jQuery('.pubguru-connected').removeClass('hidden');

				jQuery('.pg-tc-trail').toggle(!response.data.hasTrafficCop);
				jQuery('.pg-tc-install').toggle(response.data.hasTrafficCop);
			})
			.fail(function (jqXHR) {
				const response = jqXHR.responseJSON;
				const notice = jQuery(
					'<div class="notice notice-error is-dismissible" />'
				);
				notice.html(
					'<p>' +
						response.data +
						'</p>' +
						'<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>'
				);

				pubguruConnectButton.closest('.postbox').after(notice);
			})
			.complete(() => spinner.removeClass('show'));
	});
}
