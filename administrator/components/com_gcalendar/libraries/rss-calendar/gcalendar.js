window
		.addEvent(
				'domready',
				function() {
					var gcCalSlide = new Fx.Slide('gc_gcalendar_view_list');

					$('gc_gcalendar_view_toggle')
							.addEvent(
									'click',
									function(e) {
										e = new Event(e);
										gcCalSlide.toggle();

										var oldImage = window.document
												.getElementById('gc_gcalendar_view_toggle_status').src;
										var gcalImage = oldImage;
										var path = oldImage.substring(0,
												oldImage.lastIndexOf('/'));
										if (gcCalSlide.open)
											var gcalImage = path + '/btn-down.png';
										else
											var gcalImage = path + '/btn-up.png';
										window.document
												.getElementById('gc_gcalendar_view_toggle_status').src = gcalImage;
										e.stop();
									});
					gcCalSlide.hide();
				});
